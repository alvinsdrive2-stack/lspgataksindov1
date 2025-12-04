<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi TUK - LSP LPK Gataksindo</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900">
    <main class="w-full h-screen flex justify-center items-center">
        <div class="w-96 h-96 bg-gray-800 rounded-xl flex justify-center items-center flex-col gap-9">
            <div class="flex items-center justify-center gap-5">
                <img src="./images/logo.png" alt="">
                <h1 class="text-center text-2xl font-bold">Login Verifikator TUK</h1>
            </div>
            <form action="login" method="POST" class="flex justify-center items-center flex-col gap-5 w-full">
                @csrf
                <div class="flex flex-col w-80">
                    <label for="email" class="text-lg font-medium">Email</label>
                    <input type="text" name="email" class="h-10 bg-slate-400 rounded-lg text-white">
                </div>
                <div class="flex flex-col w-80">
                    <label for="password" class="text-lg">Password</label>
                    <input type="password" name="password" class="h-10 bg-slate-400 rounded-lg text-white">
                </div>
                <button class="bg-green-400 py-1 px-5 rounded-lg text-white font-medium text-xl hover:bg-green-700 transition duration-300">Login</button>
            </form>
        </div>
    </main>
</body>
</html>