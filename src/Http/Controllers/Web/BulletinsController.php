<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Models\Bulletin;
use Bishopm\Churchnet\Models\Bulletintemplate;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Meeting;
use Bishopm\Churchnet\Libraries\Fpdf\Fpdf;
use App\Http\Controllers\Controller;
use DB;

class BulletinsController extends Controller
{

    public function index()
    {
        $bulletins = $this->bulletin->all();
        return view('churchnet::bulletins.index', compact('bulletins'));
    }

    public function show($id)
    {
        $this->bulletin = Bulletin::with('bulletinitems', 'society')->find($id);
        $template = Bulletintemplate::with('bulletintemplateitems.bulletinwidget.bulletinwidgetfield')->find(1);
        $columns = json_decode($template->columncount, true);
        $colwidth = $columns[1] - $columns[0];
        $pdf = new Fpdf();
        $pdf->SetDrawColor(125, 125, 125);
        $pdf->AddPage('L');
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetFont('Arial', '', 12);
        for ($pg = 1; $pg < $template->pagecount; $pg++) {
            for ($col = 1; $col < count($columns); $col++) {
                $y = 10;
                $pdf->Line($columns[$col], 1, $columns[$col], 3);
                foreach ($template->bulletintemplateitems->sortBy('sortorder') as $titem) {
                    if (($titem->page == $pg) and ($titem->columnnumber == $col)) {
                        $widget = json_decode($titem->bulletinwidget->widgetdata);
                        $pdf->setxy($columns[$col], $y);
                        if ($widget->type == "text") {
                            $pdf->SetFont('Arial', $widget->fontweight, $widget->fontsize);
                            $pdf->multicell($colwidth, $widget->lineheight, $this->populatewidget($widget->text), 0, $widget->alignment);
                            $y = $pdf->GetY() + 10;
                        } elseif ($widget->type == "image") {
                            $pdf->Image(base_path() . $widget->filename, $columns[$col] + $widget->leftpadding, $y, $widget->width, $widget->height);
                            $y = $pdf->GetY() + 10;
                        } elseif ($widget->type == "birthdays") {
                            $pdf->SetFont('Arial', 'B', 12);
                            $pdf->cell($colwidth, 0, $widget->title, 0, 0, 'C');
                            $y = $pdf->GetY() + 6;
                            foreach ($this->populatewidget("birthdays") as $key => $day) {
                                $pdf->SetFont('Arial', 'B', 10);
                                $pdf->setxy($columns[$col], $y);
                                $pdf->cell(12,4.5,$key);
                                $pdf->setxy($columns[$col]+14, $y);
                                $pdf->SetFont('Arial', '', 10);
                                $pdf->multicell($colwidth-14, 4.5, utf8_decode(implode(', ', $day)), 0, 'L');
                                $y = $pdf->GetY();
                            }
                        } elseif ($widget->type == "diary") {
                            $y = $pdf->GetY() + 10;
                            $pdf->SetFont('Arial', 'B', 12);
                            $pdf->setxy($columns[$col], $y);
                            $pdf->cell($colwidth, 0, $widget->title, 0, 0, 'C');
                            $y = $pdf->GetY() + 4;
                            $pdf->setxy($columns[$col], $y);
                            $pdf->SetFont('Arial', '', 10);
                            foreach ($this->populatewidget("diary") as $key => $day) {
                                $y = $pdf->GetY();
                                $pdf->setxy($columns[$col], $y);
                                if ($key !== 0) {
                                    $pdf->multicell($colwidth, 5, $key . ": " . utf8_decode(implode(', ', $day)), 0, 'L');
                                } else {
                                    $pdf->multicell($colwidth, 5, utf8_decode(implode(', ', $day)), 0, 'L');
                                }
                            }
                            $y = $pdf->GetY();
                        } elseif ($widget->type == "fields") {
                            dd($titem->bulletinwidget);
                        }
                    }
                }
            }
        }
        $pdf->Output();
    }

    function populatewidget($widget)
    {
        if (strpos($widget, '[society]')) {
            return str_replace('[society]', $this->bulletin->society->society, $widget);
        } elseif ($widget == "birthdays") {
            $sun = $this->bulletin->bulletindate;
            $mon = strval(date('m-d', strtotime($sun) + 86400));
            $tue = strval(date('m-d', strtotime($sun) + 172800));
            $wed = strval(date('m-d', strtotime($sun) + 259200));
            $thu = strval(date('m-d', strtotime($sun) + 345600));
            $fri = strval(date('m-d', strtotime($sun) + 432000));
            $sat = strval(date('m-d', strtotime($sun) + 518400));
            $sun = substr($sun, 5);
            $days = array($sun, $mon, $tue, $wed, $thu, $fri, $sat);
            $indivs = Individual::insociety($this->bulletin->society_id)->wherein(DB::raw('substr(birthdate, 6, 5)'), $days)->whereNull('individuals.deleted_at')->select('individuals.firstname', 'individuals.surname', 'individuals.cellphone', 'households.homephone', 'households.householdcell', DB::raw('substr(birthdate, 6, 5) as bd'))->orderByRaw('bd')->get();
            foreach ($indivs as $indiv) {
                $bd = date('D j',strtotime('2000-' . $indiv->bd));
                $data[$bd][] = $indiv->firstname . " " . $indiv->surname;
            }
            return $data;
        } elseif ($widget == "diary") {
            $dayone = strtotime($this->bulletin->bulletindate) + 86400;
            $daylast = $dayone + 518400;
            $dates = Meeting::where('society_id',$this->bulletin->society_id)->where('meetingdatetime','>',$dayone)->where('meetingdatetime','<',$daylast)->orderBy('meetingdatetime','ASC')->get();
            $data = array();
            foreach ($dates as $dd) {
                $data[date('D j H:i',strtotime($dd->meetingdatetime))][] = $dd->description;
            }
            if (!count($data)) {
                $data[0][]="No diary entries for this week.";
            }
            return $data;
        } else {
            return $widget;
        }
    }
}
