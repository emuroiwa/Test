
<div class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container">

            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <!-- Be sure to leave the brand out there if you want it shown -->

            <!-- Everything you want hidden at 940px or less, place within here -->



            <div class="nav-collapse collapse">
                <!-- .nav, .navbar-search, .navbar-form, etc -->

                <ul class="nav">
                    <li><a href="index.php"><i class="icon-home icon-large"></i>&nbsp;Home</a></li>
                    <?php if($_SESSION['access']==1){?>
   <!--         <li>
		<a href="./index.php?page=all_pay2.php"><i class="icon-money icon-large"></i>&nbsp;Stand Payment</a>
	</li>-->	
             
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-money icon-large"></i>&nbsp;Users
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=reg.php">Create</a></li>
  <li><a href="index.php?page=manage.php">Manage </a></li>



                        </ul>
                    </li>
               <li><a href="index.php?page=NewSite.php">New Site </a></li>
               <li><a href="index.php?page=NewGenSet.php">New GenSet </a></li>

                    <!--  <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-money icon-large"></i>&nbsp;Reports
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=reg.php">Create</a></li>
  <li><a href="index.php?page=manage.php">Manage </a></li>



                        </ul>
                    </li>
             -->
          
                   
                    <?php }if($_SESSION['access']==2){?>

                     <li><a href="index.php?page=Useage.php">Useage Site </a></li>
                     <li><a href="index.php?page=UseageFuel.php">Useage Fuel </a></li>
                     <li><a href="index.php?page=TopUp.php">TopUp Site </a></li>
                     <li><a href="index.php?page=TopUpFuel.php">TopUp Fuel </a></li>

               
              <?php }
					if($_SESSION['access']==3){?>
    <li><a href="telecel" target="_blank">Reports</a></li>
              <?php }?>
              <!--      <li><a href="index.php?page=changepass.php" class="active">Help</a></li>-->

                    <li><a  href="#myModal" role="button"  data-toggle="modal"><i class="icon-signout icon-large"></i>&nbsp;Logout</a></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="hero-unit-header">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">


                <div class="row-fluid">
                    <div class="span6">
                       
                            <?php $user_query=mysql_query("select * from users where username='$_SESSION[username]'")or die(mysql_error());
                            $row=  mysql_fetch_array($user_query);
                            ?>
                            <a href="index.php?page=changepass.php" class="btn btn-info">Welcome:<i class="icon-user icon-large"></i>&nbsp;<?php echo $row['name']." ".$row['surname']; ?></a> <br>
 <!-- <img src="images/head0.png">-->
                    </div>
                    <div class="span6">

                        <div class="pull-right">
                          <i class="icon-calendar icon-large"></i><?php
                            $Today = date('y:m:d');
                            $new = date('l, F d, Y', strtotime($Today));
                            echo $new;
                            ?>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>