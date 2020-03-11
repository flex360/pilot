<div class="text-center">

    <p>Enter your password below to access this page.</p>

    <form class="" action="/page/auth" method="post" style="max-width: 400px; margin-left: auto; margin-right: auto; border: 1px solid #DDD; border-radius: 5px; padding: 15px;">

        {{ csrf_field() }}

        <div class="form-group">

            <label for="password" class="sr-only">Password</label>

            <input type="password" name="password" value="" class="form-control" placeholder="Password">

        </div>

        <input type="hidden" name="page" value="{{ $page->id }}">

        <button type="submit" name="button" class="btn btn-primary btn-block">Submit</button>

    </form>

</div>
