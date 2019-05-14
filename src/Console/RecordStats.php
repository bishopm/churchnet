<?php

namespace Bishopm\Churchnet\Console;

use Illuminate\Console\Command;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Statistic;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Payment;
use Bishopm\Churchnet\Models\Measure;

class RecordStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'churchnet:recordstats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record monthly discipleship statistics';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $societies = Society::with('services')->where('dostats',1)->get();
        $firstday = date('Y-m-01',strtotime(date('Y-m-01')) - 3600*24);
        $previousquarter = date('Y-m-01',strtotime(date('Y-m-01')) - 90*3600*24);
        $lastday = date('Y-m-d',strtotime(date('Y-m-01')) - 3600*24);
        foreach ($societies as $society) {
            $data = array();
            // WORSHIP
            $services=array();
            foreach ($society->services as $service) {
                $services[]=$service->id;
            }
            $stats = Statistic::whereIn('service_id',$services)->whereRaw('SUBSTRING(statdate, 1,  4) = ' . substr($lastday,0,4))->whereRaw('SUBSTRING(statdate, 6, 2) = ' . substr($lastday,5,2))->get();
            $worship=array();
            foreach ($stats as $stat) {
                if (!array_key_exists($stat->statdate, $worship)) {
                    $worship[$stat->statdate]['total'] = $stat->attendance;
                    $worship[$stat->statdate]['count'] = 1;
                } else {
                    $worship[$stat->statdate]['total'] = $worship[$stat->statdate]['total'] + $stat->attendance;
                    $worship[$stat->statdate]['count'] = $worship[$stat->statdate]['count'] + 1;
                }
            }
            $totalattendance=0;
            foreach ($worship as $wk) {
                $totalattendance = $totalattendance + $wk['total'];
            }
            if ($totalattendance == 0) {
                $data['worship'] = 0;
            } else {
                $data['worship'] = round($totalattendance / count($worship));
            }
            
            // SERVING
            $members = Group::with('individuals')->where('grouptype','service')->where('society_id',$society->id)->get();
            $servants = array();
            foreach ($members as $servant) {
                if (!in_array($servant->id,$servants)) {
                    $servants[] = $servant->id;
                }
            }
            $data['service'] = count($servants);

            // FELLOWSHIP
            $fmembers = Group::with('individuals')->where('grouptype','fellowship')->where('society_id',$society->id)->get();
            $fellows = array();
            foreach ($fmembers as $fellow) {
                if (!in_array($fellow->id,$fellows)) {
                    $fellows[] = $fellow->id;
                }
            }
            $data['fellowship'] = count($fellows);

            // GROWTH
            $data['growth'] = User::where('last_access','>=',$firstday . ' 00:00:00')->where('last_access','<=',$lastday . ' 23:59:59')->count();

            // GIVING
            $data['giving'] = 0;
            $givers = array();
            $payments = Payment::where('society_id',$society->id)->where('paymentdate','>=',$previousquarter)->get();
            foreach ($payments as $payment) {
                if (!in_array($payment->pgnumber,$givers)) {
                    $givers[]=$payment->pgnumber;
                    $data['giving']++;
                }
            }
            $measure = Measure::create([
                'society_id' => $society->id,
                'measureyear' => intval(substr($firstday,0,4)),
                'measuremonth' => intval(substr($firstday,6,2)),
                'worship' => $data['worship'],
                'connect' => $data['fellowship'],
                'give' => $data['giving'],
                'serve' => $data['service'],
                'grow' => $data['growth']
            ]);
        }      
        return $data;
    }
}
