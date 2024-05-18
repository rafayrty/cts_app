<!-- resources/views/results/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <!-- Include Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6">Results For {{$quiz->name}}</h1>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Score</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quiz ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created At</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $result)
                        @php
                            $user = \Chiiya\FilamentAccessControl\Models\FilamentUser::find($result->user_id);
                            $email = null;
                            if($user){
                                $email = $user->email;
                            }
                        @endphp
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->id }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->score }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->total }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->quiz_id }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $email}}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->created_at }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (session('message'))
            <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
    </div>
</body>
</html>
