<!-- resources/views/filament/widgets/user-stats.blade.php -->
<div>
<head>
            <link href="resources/css/app.css" rel="stylesheet">
</head>

<div class="flex [&>*]:flex flex-row sm:flex-col gap-2 grow [&>*]:flex [&>*]:grow">

    <div class="grow p-4 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h2 class="text-xl font-bold mb-4 ">User Stats</h2>

        <p>Total users: <strong>{{ $userCount }}</strong></p>
    </div>


    <div class="grow p-4 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-lg font-semibold mt-4">Latest Users</h3>
        <ul class="list-disc list-inside">
            @foreach ($latestUsers as $user)
                <li>{{ $user->name }} ({{ $user->email }})</li>
            @endforeach
        </ul>
    </div>
    
    <div class="grow p-4 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-lg font-semibold mt-4">Latest Users</h3>
        <ul class="list-disc list-inside">
            @foreach ($latestUsers as $user)
                <li>{{ $user->name }} ({{ $user->email }})</li>
            @endforeach
        </ul>
    </div>

</div>
</div>