<!DOCTYPE html>
<html lang="en">
    @auth
        @include('template.meta')
    @endauth
<body>
    <div class="error-wrapper">
        <div class="error-content">
            <div class="text-center">
                <img src="{{ asset('images/warning.png') }}" alt="Warning Icon" />
                <h1>Unauthorized Access</h1>
                <h3>PAGE NOT FOUND</h3>
                <p>
                    The page you're trying to access is restricted or unavailable.<br/>
                    Please check your permission or contact support for assistance.
                </p>

                <a href="{{ url()->previous() }}" class="btn">Back to Home</a>
            </div>
        </div>
    </div>

    <style>
        .error-wrapper {
            background:
                linear-gradient(
                    rgba(255, 255, 255, 0.8),
                    rgba(255, 255, 255, 0.8)
                ),
                url("{{ asset('images/wallpaper-covered.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .error-content {
            width: 100%;
            height: 100vh;
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-content a:hover {
            background-color: #ff9200;
            color: #fff;
        }

        .error-content img {
            height: 10rem;
        }

        .error-content h1 {
            font-size: 7rem;
            font-weight: 700;
            color: #ff8300;
        }

        .error-content h3 {
            font-size: 3.5rem;
            font-weight: bold;
            color: #3f3f46;
            letter-spacing: 3px
        }

        .error-content p {
            font-size: 1.65rem;
            letter-spacing: 1px;
            color: #3f3f46;
        }

        .error-content a {
            background-color: #ff8300;
            color: #fff;
            font-weight: 800;
            padding: 5px 15px;
        }
    </style>

    @auth
        @include('script.scripts')

        @include('script.configurations_scripts')
    @endauth
</body>
</html>
