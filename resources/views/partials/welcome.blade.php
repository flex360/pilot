<div class="home-wrapper">
    <div class="home-brand">
        <h1>Pilot</h1>
        <svg aria-hidden="true" data-prefix="fas" data-icon="paper-plane" class="svg-inline--fa fa-paper-plane fa-w-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path fill="currentColor" d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"/>
        </svg>
    </div>
    <div class="get-started">
        {{-- <a href="https://github.com/flex360/pilot/blob/master/readme.md" target="_blank" class="btn">Get Started</a> --}}
        <a href="/pilot" target="_blank" class="btn">Login</a>
    </div>
</div>

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Bellota:wght@700&display=swap" rel="stylesheet">
<style>
body, html { paddding: 0; margin: 0; }
.home-wrapper {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100vh;
    justify-content: center;
    align-items: center;
    font-family: 'Bellota', cursive;
    font-weight: 700;
    color: #444;
}
.home-brand {
    display: flex;
    flex-direction: row;
    margin-bottom: 1rem;
}
.home-wrapper h1 {
    font-size: 4rem;
    margin: 0;
}
.svg-inline--fa.fa-paper-plane {
    width: 40px;
    height: 40px;
    margin-left: 10px;
    margin-bottom: 10px;
    margin-top: 16px;
    transform: rotate(20deg);
}
.get-started .btn {
    background-color: #29c7ac;
    padding: 12px 20px;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    margin: 0 5px;
    text-decoration: none;
    font-family: sans-serif;
    font-weight: 300;
    text-transform: uppercase;
}
.get-started .btn:hover {
    background-color: #01ab8e;
}
</style>
@endpush