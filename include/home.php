<html> 
  <head> 
    <script src="jquery.js"></script> 
    <script> 
    $(function(){
      $("#includedContent").load("navbar.html"); 
    });
    </script> 
  </head> 

  <body> 
  <link href="navbar.html" rel="import" />

    <!-- Search form -->
    <form class="form-inline">
      <i class="fas fa-search" aria-hidden="true"></i>
      <input class="form-control form-control-sm ml-3 w-75" type="text" placeholder="Search"
        aria-label="Search">
    </form>

    <select class="mdb-select md-form colorful-select dropdown-primary" searchable="Search here..">
      <option value="1"> Genre </option>
      <option value="2"> Movie </option>
      <option value="3"> Television Series </option>
      <option value="4"> Actor </option>
      <option value="5"> Average Rating </option>
    </select>

    <label class="mdb-main-label">Example label</label>
  </body> 


</html>


