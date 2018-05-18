@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            This site hosts the API for Churchnet churches using <a target=_"blank" href="https://github.com/bishopm/connexion">Connexion</a> - free church software with the following features:
            <br><br>
            <li><b>membership database</b> - members can be assigned to groups and can be emailed and sms'ed from within the system</li>
            <li><b>member rosters</b> - set up duty rosters and send reminders by SMS</li>
            <li><b>pastoral care</b> - build up a history of pastoral contact, track birthdays and anniversaries</li>
            <li><b>statistics</b> - track and graph worship attendance</li>
            <li><b>project management</b> - todo lists assigned to members and regular email reminders</li>
            <li><b>worship planner</b> - songs (optionally with chords) and liturgy can be stored and usage tracked</li>
            <li><b>website manager</b> - upload blogs, sermons or static content and manage your website</li>
            <li><b>HR function</b> - record staff details and leave days</li>
            <li><b>bookshop manager</b> - manage a small church-based bookshop and integrate with website</li>
            <br>
            An additional module for MCSA societies or circuits adds the ability to:
            <li><b>Map</b> each society in the circuit</li>
            <li>Store preacher details and produce a quarterly <b>preaching plan</b> online</li>
            <p>Please note: This is not an official project of the MCSA. To visit the MCSA homepage, click <a target="_blank" href="http://churchnet.org.za">here</a>.</p>
            <p>To set up the software, you'll need someone familiar with installing software on a webserver. Instructions are on the project's <a target=_"blank" href="https://github.com/bishopm/connexion">Github page</a></p>
            <p>{{ HTML::mailto('admin@church.net.za','Email us') }} if you need more details</p>
        </div>
    </div>
</div>
@endsection