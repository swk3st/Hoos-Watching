<section id="navbar">
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Hoo's Watching</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav w-100">
        <li class="nav-item active">
          <a class="nav-link" href="home.php"> Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="profile.php"> Profile <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="favorites.php"> Favorites <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="watchlist.php"> Watchlist <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="friends.php"> Friends <span class="sr-only">(current)</span></a>
        </li>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="favoritepeople.php"> Favorite People <span class="sr-only">(current)</span></a>
        </li>
        <?php global $user;
        if (!$user->is_logged_in()) : ?>
          <form class="ml-auto form-inline">
            <a class="btn btn-primary btn-sm mr-3" href="./sign_in.php">Log In</a>
            <a class="btn btn-primary btn-sm" href="./sign_up.php">Sign Up</a>
          </form>
        <?php else : ?>
          <form action="logged_out.php" method="post" class="ml-auto form-inline">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <a class="input-group-text" id="inputGroup-sizing-sm" href="./profile.php?email=<?php echo $user->get_email(); ?>">
                  Logged in as <?php echo $user->get_email(); ?>
                </a>
              </div>
              <div class="input-group-append">
                <button type="submit" class="btn btn-primary" type="button" name="logout" value="1">Log out</button>
              </div>
            </div>
          </form>
        <?php endif; ?>
    </div>
  </nav>
</section>