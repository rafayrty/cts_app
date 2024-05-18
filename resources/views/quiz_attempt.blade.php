<!-- resources/views/question.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question</title>
    <!-- Include Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto mt-10">
    @php
        $i = 1;
    @endphp

    <h1> {{$quiz->name}} </h1>
    @foreach ($quiz->questions as $question)
        <form action="{{ route('quiz.submit',$quiz->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Q{{$i}}: {{$question['question']}}</h2>
            @csrf
            <div class="mb-4">
                <div class="flex items-center mb-2">
                    <input class="form-radio h-4 w-4 text-indigo-600" type="radio" id="option_a" name="answer[{{$i - 1}}]" value="a">
                    <label for="option_a" class="ml-2">{{ $question['option_a'] }}</label>
                </div>
                <div class="flex items-center mb-2">
                    <input class="form-radio h-4 w-4 text-indigo-600" type="radio" id="option_b" name="answer[{{$i - 1}}]" value="b">
                    <label for="option_b" class="ml-2">{{ $question['option_b'] }}</label>
                </div>
                <div class="flex items-center mb-2">
                    <input class="form-radio h-4 w-4 text-indigo-600" type="radio" id="option_c" name="answer[{{$i - 1}}]" value="c">
                    <label for="option_c" class="ml-2">{{ $question['option_c'] }}</label>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Submit</button>
        </form>
    @endforeach

    @if (session('message'))
        <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
</div>

</body>
</html>
