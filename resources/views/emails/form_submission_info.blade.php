<h1>Form Submission for form {{$formSubmission->form}}</h1>
<ul>
@foreach($formSubmission->content as $key=>$value)
    <li> <strong>{{$key}}:</strong> {{$value}} </li>
</ul>
