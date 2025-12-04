<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Verifikasi TUK - LSP LPK Gataksindo</title>
        @vite('resources/css/app.css')
    </head>
    <body>
        <div class="bg-white max-h-screen h-screen w-screen flex flex-col">
            <div
                class="h-2/5 w-screen flex flex-col justify-center items-center gap-6"
            >
                <h1 class="text-4xl font-medium text-black">Selamat datang di Verifikasi TUK</h1>
                <img src="/images/logo-banner.png" alt="" width="160" height="140" />
            </div>
            <div
                class="bg-[#2D3580] h-3/5 w-screen flex flex-col justify-center items-center gap-6"
                style="
                    border-top-left-radius: 50% 200px;
                    border-top-right-radius: 50% 200px;
                "
            >
                <h1 class="text-4xl text-white font-medium">Login Sebagai Apa?</h1>
                <div class="flex items-center justify-center gap-7">
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/login"
                        target="blank"
                        ><img src="images/reguler.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">
                            Admin LSP
                        </h1>
                    </a>
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/login-direktur"
                        target="blank"
                        ><img src="images/fg.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">Direktur LSP</h1>
                    </a>
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/login-verifikator"
                        target="blank"
                        ><img src="images/balai.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">Verifikator</h1>
                    </a>
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/login-tuk"
                        target="blank"
                        ><img src="images/balai.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">Ketua TUK</h1>
                    </a>
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/login-validator"
                        target="blank"
                        ><img src="images/balai.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">Validator</h1>
                    </a>
                    <a
                        class="w-52 h-52 bg-white hover:bg-[#E4242A] transition duration-150 rounded-xl flex flex-col items-center justify-center shadow-2xl"
                        href="/archive"
                        target="blank"
                        ><img src="images/fg.svg" alt="" />
                        <h1 class="capitalize font-medium text-xl text-black">Archive Surat Verifikasi</h1>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
