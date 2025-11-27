<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Book Check-In - {{ config('app.name', 'Library Database') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center p-6">
        <div class="max-w-3xl w-full bg-white dark:bg-gray-800 rounded-lg shadow p-8">
            <header class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">Book Check-In</h1>
                <a href="{{ url('/') }}" class="px-3 py-1 border rounded">Home</a>
            </header>

            <!-- Flash messages -->
            @if(session('success'))
                <div class="mt-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('books.check-in.form') }}" class="space-y-4 mb-6">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ISBN</label>
                        <input name="isbn" type="text" value="{{ old('isbn', $isbn ?? '') }}" class="mt-1 block w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Card ID</label>
                        <input name="card_id" type="text" value="{{ old('card_id', $card_id ?? '') }}" class="mt-1 block w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Borrower Name</label>
                        <input name="name" type="text" value="{{ old('name', $name ?? '') }}" placeholder="substring" class="mt-1 block w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Search</button>
                    <a href="{{ route('books.check-in.form') }}" class="px-4 py-2 border rounded">Reset</a>
                </div>
            </form>

            @if(isset($results) && $results)
                <form method="POST" action="{{ route('books.check-in') }}">
                    @csrf
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Select up to 3 active loans to check in.</p>
                    <ul class="space-y-2">
                        @foreach($results as $loan)
                            <li class="p-3 border rounded bg-gray-50 dark:bg-gray-700 flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <input type="checkbox" name="loan_ids[]" value="{{ $loan->Loan_id }}" class="loan-checkbox mt-1" />
                                </div>
                                <div class="flex-1 text-sm">
                                    <div class="font-medium">Title: {{ $loan->book->Title }}</div>
                                    <div class="font-medium">ISBN: {{ $loan->Isbn }}</div>
                                    <div class="text-gray-600 dark:text-gray-300">Card ID: {{ $loan->Card_id }}</div>
                                    <div class="text-gray-600 dark:text-gray-300">Borrower: {{ $loan->borrower->Bname ?? '—' }}</div>
                                    <div class="text-gray-600 dark:text-gray-300">Date Out: {{ optional($loan)->Date_out }}</div>
                                    <div class="text-gray-600 dark:text-gray-300">Due: {{ optional($loan)->Due_date }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">Check In Selected</button>
                        <a href="{{ route('books.check-in.form') }}" class="px-4 py-2 border rounded">Back</a>
                    </div>

                    <div class="mt-3 text-sm text-gray-600">{{ $results->links() }}</div>
                </form>
            @endif
        </div>

        <script>
            // Limit selection to 3 checkboxes
            document.addEventListener('DOMContentLoaded', function () {
                const checkboxes = document.querySelectorAll('.loan-checkbox');
                function enforceLimit() {
                    const checked = Array.from(checkboxes).filter(c => c.checked);
                    if (checked.length >= 3) {
                        checkboxes.forEach(c => { if (!c.checked) c.disabled = true; });
                    } else {
                        checkboxes.forEach(c => { c.disabled = false; });
                    }
                }
                checkboxes.forEach(cb => cb.addEventListener('change', enforceLimit));
            });
        </script>
    </body>
</html>