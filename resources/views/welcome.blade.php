<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="MyNutrition">
        <meta property="og:description" content="Powered by Laravel">
        <meta property="og:image" content="{{ asset('img/mynutritionlogo_upscaled.jpg') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">

        <title>MyNutrition</title>

        
        <!-- Icon -->
        <link rel="icon" href="{{ asset('img/mynutritionlogo_scales_upscaled.png') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        {{-- <link rel="stylesheet" href="dist/fontawesome-5.11.2/css/all.min.css" /> --}}
        <!-- Styles --> 
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link href="resources/css/app.css" rel="stylesheet">

        <script
                src="https://code.jquery.com/jquery-3.7.0.min.js"
                integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
                crossorigin="anonymous"></script>
        <script
                src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
                integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
                crossorigin="anonymous"></script>
        <!--         -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>




    </head>

    <!-- credit to: https://ibelick.com/blog/create-grid-and-dot-backgrounds-with-css-tailwind-css -->

    <body class="figtree-300 font-semibold montserrat-800 antialiased mx-auto  overflow-x-hidden bg-white min-h-screen l bg-gradient-to-r from-white via-orange-200/50 to-white to-90%">
     
      <div class="bg-[radial-gradient(#FFFFFF_3px,transparent_3px)] [background-size:64px_64px]">
      <header>
        <nav id="navbar" class="grid grid-cols-[1fr_1fr] sm:flex px-6 sm:px-0 justify-between sm:justify-around items-center sm:mx-auto text-3xl /font-extrabold / /sticky top-0 z-50 bg-transparent max-w-[1600px]">
            <img class="sm:mx-auto md:mx-0" src="{{ asset('img/mynutritionlogo_upscaled.png')}}" width="128" height="128" alt="">

            <div class="flex hidden sm:flex sm:flex-row justify-around text-[#CF1909] gap-8">
                <a href="{{ route('filament.admin.auth.login') }}">Login</a>

                {{-- <a href="{{ route('filament.admin.auth.register') }}">Register</a> --}}
            
                <a href="#contacts-footer">Contact</a>

                {{-- <a href="#contacts-footer">Premium</a> --}}
            </div>

            <i id="mobile-hamburger-btn" class="mobile-hamburger-btn cursor-pointer justify-self-end sm:hidden fa-solid fa-bars "></i>

            <i id="mobile-hamburger-btn-exit" class="fa-solid fa-xmark cursor-pointer hidden justify-self-end sm:hidden fa-solid fa-bars "></i>

            <div id="login-mobile" class="col-span-2 login-mobile hidden sm:hidden justify-around text-[#CF1909] gap-8 text-2xl">

                <div class="flex flex-col gap-6 text-4xl justify-center [&>*]:grid [&>*]:grid-cols-[1fr_6fr] ">
                    <a href="{{ route('filament.admin.auth.login') }}"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        
                    {{-- <a href="{{ route('/admin/register') }}"><i class="fa-solid fa-user-plus"></i></i> Register</a> --}}
        
                    <a href="#contacts-footer"><i class="fa-solid fa-address-book"></i>Contact</a>
                </div>
            </div>
        </nav>

        
      </header>

      {{-- <header class="bg-orange-500 h-32 w-full my-4 flex items-center justify-center opacity-60 hidden">

        <p class="text-white text-3xl">INTRODUCTORY OFFER: 50% OFF MyNutrition Premium if you preorder!</p>

      </header> --}}

      <main class="container mx-auto overflow-hidden sm:overflow-visible sm:p-0">
        <div id="desktop-hero" class="mt-16 sm:mt-0 xl:grid xl:grid-cols-[2fr_3fr] items-center relative  h-full mx-auto">

            <div class="hero-text text-5xl md:text-8xl /font-extrabold relative text-center md:text-left md:my-24 text-white md:text-[#CF1909] py-12 z-50 relative">

                {{-- <div class="bg-black sm:hidden z-0 absolute top-0 w-full h-full opacity-80"></div> --}}

                <div class="mx-auto w-max" data-aos="fade-left">Conquer your</div> 
                
                <div id="carousel-container" class="flex gap-4 items-center /justify-start relative p-4 mx-auto /xl:mx-0 overflow-hidden max-w-[241px] md:max-w-[327.017px] xl:max-w-[480.767px] h-[128px]">
                    


                    <div id="carousel-text" style="" class="flex items-center /gap-4 relative  /[&amp;>*]:flex-1">

                        <div id="carousel-wrapper" class="carousel-wrapper md:text-8xl absolute flex mx-auto max-w-[241.367px] overflow-hidden sm:max-w-[600px] scroll-smooth" style="left: -1rem;">

                            <div class="carousel-slides font-light flex xl:w-[400%] xl:justify-evenly text-center [&>*]:w-[241.367px] md:[&>*]:w-max xl:[&>*]:w-[480.767px]">
                                <div class="slide bg-orange-500 text-white p-4 rounded-lg" style="">fitness</div>
                                <div class="slide bg-orange-500 text-white p-4 rounded-lg" style="">nutrition</div>          
                                <div class="slide bg-orange-500 text-white p-4 rounded-lg" style="">hydration</div>
                                <div class="slide bg-orange-500 text-white p-4 rounded-lg" style="">health</div>
                            </div>

                        </div>
                    </div>
                    

                </div>      


                <div class="mx-auto text-center" data-aos="fade-right">goals</div>
                
                <div class="md:absolute md:right-[31.33%] xl:-right-[24.33%] bg-orange-800 md:text-white md:p-4 z-40 rounded-lg select-none mt-4 w-1/2 sm:w-initial mx-auto w-fit p-8 md:p-0"><span>to</span><span class="md:text-white">day</span></div>

            </div>
            
            <div id="hero-images" class="absolute top-0 md:relative z-0  brightness-50 sm:brightness-100 ">

                <div class="relative ml-0 sm:ml-4 overflow-hidden h-full [&>img]:object-cover z-30 transition duration-300 ease-in-out animate-border h-[125%] w-[125%] sm:h-initial sm:w-initial" style="border-radius:
    83% 17% 90% 10% / 23% 84% 16% 77%;">
                    <img id="img-1" src="{{ asset('img/pexels-maksgelatin-4348629.jpg') }}" class="img-1 top-0 rounded-lg /opacity-70 z-50"  alt="">

                    <img id="img-2" src="{{ asset('img/pexels-ella-olsson-572949-1640774.jpg') }}" class="absolute img-2 top-0 bg-gradient-to-r from-transparent to-orange-500 z-0 left-0 /opacity-60" alt="">

                    <img id="img-3" src="{{ asset('img/pexels-olly-3776811.jpg') }}" class="absolute img-3 top-0 bg-gradient-to-r from-transparent to-orange-500 z-0 left-0 /opacity-60" alt="">

                    <img id="img-4" src="{{ asset('img/pexels-pixabay-53404.jpg') }}" class="absolute img-4 top-0 bg-gradient-to-r from-transparent to-orange-500 z-0 left-0  /opacity-60" alt="">


                    <div class="absolute sm:-bottom-8 -bottom-0 -right-8 bg-orange-600 rounded-full sm:w-48 sm:h-48 w-16 h-16"></div>

                    <div class="absolute sm:-top-8 -left-8 bg-orange-800 rounded-full sm:w-48 sm:h-48 w-16 h-16 z-10"></div>
                </div>

            </div>
            

            {{-- <div class="w-full h-32 bg-gradient-to-r from-orange-600 to-orange-400 flex items-center justify-center rounded-tr-lg relative">

                <div class="absolute -top-16 -left-4 bg-orange-400 rounded-full w-32 h-32 z-50 md:z-0"></div>

                <p class="text-white text-6xl font-black">Features</p>
    
                <div class="absolute -bottom-16 -right-4 bg-orange-600 rounded-full w-32 h-32 bg-white"></div>

            </div> --}}

    

        </main>

        
        <div id="features" data-aos="fade-up" class="relative grid grid-cols-1 px-12 md:px-0 md:grid-cols-6 mt-32 mx-auto text-orange-600 max-w-[1400px] [&>*]:text-center gap-12 /[&>*>img]:h-[421px] /[&>*>img]:h-[421px] [&>*>img]:object-cover">

            <div id="fork" class="absolute top-[-55px] sm:left-[-10rem] sm:block">
                <img class="h-[120px] sm:h-[421px]" src="{{ asset('img/Fork.png')}}" alt="" data-aos="fade-right">
            </div>

            <div id="knife" class="absolute top-[-55px] sm:top-[1350px] right-[-20px] sm:right-[-16rem] sm:bottom-0 sm:block">
                <img class="h-[120px] sm:h-[421px]" src="{{ asset('img/Knife.png')}}" alt="" data-aos="fade-left">
            </div>

            <div class="md:col-span-6 inline-block bg-gradient-to-r from-orange-400 via-red-500 to-orange-400 bg-clip-text text-5xl md:text-7xl text-transparent font-black">FEATURES</div>
            
            <div class="text-center text-4xl font-semibold mt-8 md:col-span-2" data-aos="fade-up" data-aos-duration="500">
                <p>Nutrition Tracking</p>
                
                <img class="mt-8 rounded-lg" src="{{ asset('img/mynutrition_nutritiontracking.png')}}" alt="">

                <p class="mt-8">Calories, Fat, Carbs and Protein are all tracked.</p>
            </div>
            <div class="text-center text-4xl  font-semibold mt-8 md:col-span-2"  data-aos="fade-up" data-aos-duration="750">
                
                <p>Water Reminders</p>

                <img class="mt-8 rounded-lg" src="{{ asset( 'img/mynutrition_water.png' ) }}">

                <p class="mt-8">
                    Hydration is important, and you can track that down too!
                </p>
            </div>
            <div class="text-center text-4xl font-semibold mt-8 md:col-span-2"  data-aos="fade-up" data-aos-duration="1250">
                <p>Meal Logging</p>

                <img class="mt-8 rounded-lg" src="{{ asset( 'img/mynutrition_meallog.png') }}">

                <p class="mt-8">
                    Track your meals, at any custom meal times you wish.
                </p>
            </div>

            <div class="md:col-span-6 mt-24 sm:mt-0 inline-block bg-gradient-to-r from-orange-400 via-red-500 to-orange-400 text-white p-4 md:p-12 rounded-full /bg-clip-text text-3xl md:text-7xl text-transparent font-black relative" data-aos="fade-up" data-aos-duration="1250">
                Premium FEATURES
                
                <div class="absolute right-0 -top-[77%] md:-top-[47%]">    <img class="rounded-lg mx-auto /faded-x-mask w-[9rem] h-[9rem]" src="{{ asset( 'img/premium_crown.png') }}" data-aos="fade-up" data-aos-duration="1500">
</div>
            </div>

            <div class="text-center text-3xl sm:text-4xl font-semibold mt-8 md:col-span-3 [&>*>img]:h-[421px]"  data-aos="fade-up" data-aos-duration="1250">
                <p class="text-[#CF1A09] font-black border-b-4 sm:border-0 pb-4">Planning Meals</p>

                <img class="mt-8 rounded-lg mx-auto /faded-x-mask max-w-[256px] sm:max-w-[400px] rounded-full" src="{{ asset( 'img/clock_transparent.png') }}" data-aos="fade-up" data-aos-duration="1500">

                <p class="mt-8 text-black text-xl sm:text-4xl">
                    Plan meals in advance, with the ability to change and edit at any time.
                </p>
            </div>

            <div class="text-center text-3xl sm:text-4xl font-semibold mt-8 md:col-span-3 [&>*>img]:h-[421px]"  data-aos="fade-up" data-aos-duration="1250">
                <p class="text-[#CF1A09] font-black border-b-4 sm:border-0 pb-4">✨AI Auto Fill✨</p>

                <img  class="mt-8 rounded-lg mx-auto /faded-x-mask rounded-lg max-w-[256px] sm:max-w-[400px]" src="{{ asset( 'img/aiautofill_transparent_v2.png' )}}" alt="">

                <p class="mt-8 text-black text-xl sm:text-4xl">
                    Supercharge your food meal intake by filling it with nutritional information more quickly.
                </p>
            </div>

        </div>

        <div class="flex flex-col mt-16 " id="slogan">
            <div class="flex flex-col xl:flex-row justify-center items-center [&>p]:text-orange-600 [&>p]:text-7xl gap-4 h-fit">
                <p class="text-orange-600 font-black text-2xl">Whether</p>
                <img class="w-[250px] md:w-[350px]" src="{{ asset('img/dietimage_cropped.png')}}" width="350" alt="" data-aos="fade-up">        
                <p class="font-black text-2xl">OR</p>
                <img  class="w-[350px] md:w-[450px]" src="{{ asset('img/exerciseimage_cropped.png')}}" width="400" alt=""  data-aos="fade-down">
                <p class="hidden xl:flex">,</p>
            </div>

            <div class="flex justify-center items-center p-16 [&>p]:text-black [&>p]:text-5xl [&>p]:mt-8 sm:[&>p]:text-7xl gap-6 text-center">
                <p>MyNutrition's got you covered.</p>
            </div>

        </div>

        {{-- <div class="bg-orange-600 w-full sm:h-[50vh] /[&>*]:px-16 [&>*]:text-white [&>*]:text-center flex place-items-center rounded-tr-lg  mx-auto items-center relative overflow-x-scroll text-center">

                <div id="carousel-container" class="flex h-full place-items-center justify-start gap-4 text-center [&>*]:shrink-0 relative">

                    <div class="flex items-center justify-center selected absolute -left-2 text-2xl top-1/2 prev-slide w-12 h-12 bg-blue-500 rounded-full">

                        <
                        
                    </div>


                    <div class="flex items-center justify-center selected absolute -right-2 text-2xl top-1/2 next-slide w-12 h-12 bg-blue-500 rounded-full"> 

                        >

                    </div>
                    
                    
                    <div id="slides" class="bg-orange-800 flex items-center h-full overflow-x-scroll w-[640px] [&>*]:w-[640px] [&>*]:shrink-0 snap-x [&>*]:snap-center scroll-smooth">
                        <div>
                            <p class="text-5xl font-extrabold">Meal Logging</p>
                            <p class="pt-4 text-2xl">Log your meals from a pre-existing database.</p>
                        </div>

                        <div>
                            <p class="text-5xl font-extrabold">Dashboards</p>
                            <p class="pt-4 text-2xl">See how far your progress has come.</p>
                        </div>

                        <div>
                            <p class="text-5xl font-extrabold">Water Tracking</p>
                            <p class="pt-4 text-2xl">Track your fluid intake to stay hydrated.</p>
                        </div>

                        <div>
                            <p class="text-5xl font-extrabold">Meal Planning</p>
                            <p class="pt-4 text-2xl">To prevent over/undereating,<br>you can plan meals in the future.</p>
                        </div>

                        <div>
                            <p class="text-5xl font-extrabold">Macro Goals</p>
                            <p class="pt-4 text-2xl">Set your calorie goals.</p>
                        </div>

                        
                        <div>
                            <p class="text-5xl font-extrabold">Achievements</p>
                            <p class="pt-4 text-2xl">Cause no achievement goes unnoticed!</p>
                        </div>
                    </div>
                </div>

                <div>
                    

                </div>


                <div></div>
                <div></div>
        </div>  --}}

        <div style="position:relative;flex:none" class="">
            <div id="wave-container" class="">
                <svg class="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#FC6400" class="bg-gradient-to-t from-orange-500 to-orange-800" fill-opacity="1" d="M0,192L120,176C240,160,480,128,720,138.7C960,149,1200,203,1320,229.3L1440,256L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path></svg>
            </div>
        </div>
        
      </div>

      <footer id="contacts-footer" class="/sticky bottom-0 bg-gradient-to-b from-orange-500 to-orange-800 text-white text-4xl flex flex-col items-center justify-center border-t-4 border-t-orange-500">

        <div class="grid grid-cols-1 sm:grid-cols-[1fr_3fr] w-full max-w-[1600px]">
          
            <div>
                <img src="{{ asset('img/MyNutrition_white.png')}}" alt="" class="p-16 xl:pr-8 border-r-4 border-r-white/50">
            </div>

            <div class="grid grid-cols-2 [&>*>*]:text-lg xl:[&>*>*]:text-2xl xl:[&>*>*>h2]:text-4xl">
                <ul class="flex flex-col gap-4 ml-8 sm:ml-16 justify-center h-max sm:h-full">
                    <li class="font-black"><h2>Socials</h2></li>
                    <li><a href="https://www.linkedin.com/in/demie-mistica-049779296/"><i class="fab fa-linkedin w-[22.5px] xl:w-[30px] text-center"></i>  LinkedIn</a></li>
                    <li><a href="https://github.com/RogueStar112"><i class="fab fa-github w-[22.5px] xl:w-[30px] text-center"></i> GitHub</a></li>
                    <li><i class="fab fa-discord"></i>  Discord</li>
                </ul>
                

                <ul class="flex flex-col gap-4 ml-8 sm:ml-16 justify-center">
                    <li class="font-black"><h2>Other Projects</h2></li>
                    <li><a href="https://roguestar112.github.io/mybudget-oct-2023-frontend/">MyBudget</a></li>
                    <li><a href="https://www.demie-mistica.com">Portfolio</a></li>
                    <li class="border-t-2 border-white pt-4"><a href="#navbar">Back to top</a></li>

                </ul>
            </div>


            <p class="col-span-full text-center py-12">&copy; 2025 Demie M.</p>  

        </div>



      </footer>

      <div class="absolute bottom-0">
      {{-- <svg class="fixed bottom-0 z-0 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgb(208 56 1 / var(--tw-bg-opacity, 1))" fill-opacity="1" d="M0,288L48,272C96,256,192,224,288,197.3C384,171,480,149,576,165.3C672,181,768,235,864,250.7C960,267,1056,245,1152,250.7C1248,256,1344,288,1392,304L1440,320L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg> --}}

      {{-- <svg class="fixed bottom-0 z-0 opacity-80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#d03801" fill-opacity="1" d="M0,256L30,250.7C60,245,120,235,180,234.7C240,235,300,245,360,245.3C420,245,480,235,540,218.7C600,203,660,181,720,144C780,107,840,53,900,26.7C960,0,1020,0,1080,48C1140,96,1200,192,1260,218.7C1320,245,1380,203,1410,181.3L1440,160L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z"></path></svg> --}}
      </div>

    </div>

    </body>

    {{-- <script>
        let index = 0;
        const slides = document.querySelector('.carousel-slides');
        const totalSlides = document.querySelectorAll('.carousel-slides div').length;

        const slides_List = document.querySelectorAll('.carousel-slides div');

        function showSlide(n) {
            // index = (n + totalSlides) % totalSlides;
            slides.style.transform = `translateX(${-slides_List * 100 / totalSlides}%)`;
        }

        function nextSlide() { showSlide(index + 1); }
        function prevSlide() { showSlide(index - 1); }

        setInterval(nextSlide, 3000); // Auto-slide every 3 seconds
    </script> --}}

    <script>
        let index = 0;
        const carousel = document.querySelector('.carousel-slides');
        const images = document.querySelectorAll('.carousel-slides div');

        const carousel_container = document.getElementById('carousel-container');
        let offsets = [];

        // Calculate dynamic widths and offsets
        function calculateOffsets() {
            offsets = [];
            let totalOffset = 0;
            images.forEach((img) => {
                offsets.push(totalOffset);
                totalOffset += img.clientWidth; // Add margin-right spacing
            });
        }

        function showSlide(n) {
            index = (n + images.length) % images.length;
            carousel.style.transform = `translateX(-${offsets[index]}px)`;
            carousel_container.style.maxWidth = `${images[index].clientWidth}px`;
        }

        function nextSlide() { showSlide(index + 1); }
        function prevSlide() { showSlide(index - 1); }

        // Recalculate offsets after images load
        window.onload = () => {
            calculateOffsets();
            window.addEventListener('resize', calculateOffsets); // Adjust on resize
        };

        setInterval(nextSlide, 3000); // Auto-slide every 3 seconds

        const mobile_hamburger_btn = document.getElementById('mobile-hamburger-btn');
        const mobile_hamburger_btn_exit = document.getElementById('mobile-hamburger-btn-exit');
        const login_mobile = document.getElementById('login-mobile');

        
        mobile_hamburger_btn.addEventListener('click', function(){

            mobile_hamburger_btn.classList.toggle('hidden');
            mobile_hamburger_btn_exit.classList.toggle('hidden');
            login_mobile.classList.toggle('hidden');
    
        })

        mobile_hamburger_btn_exit.addEventListener('click', function(){

        mobile_hamburger_btn.classList.toggle('hidden');
        mobile_hamburger_btn_exit.classList.toggle('hidden');
        login_mobile.classList.toggle('hidden');

        })

    </script>

    
    <script>
    AOS.init();
    </script>
</html>
