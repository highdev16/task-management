<!-- Text input -->
<div class="form-group">
    <label class="col-md-4 control-label">Username</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="user_name" placeholder="Username" class="form-control" required="" value="<?php echo ($edit) ? $admin_account['user_name'] : ''; ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-4 control-label">Full name</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="full_name" placeholder="Full Name" class="form-control" required="" value="<?php echo ($edit) ? $admin_account['full_name'] : ''; ?>" autocomplete="off">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-4 control-label">Birthday</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="birthday" placeholder="YYYY/MM/DD" class="form-control" required="" value="<?php echo ($edit) ? $admin_account['birthday'] : ''; ?>" autocomplete="off">
        </div>
    </div>
</div>
<!-- Password input -->
<div class="form-group">
    <label class="col-md-4 control-label">Password</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" id='password' name="password" placeholder="Password" class="form-control" required="" autocomplete="off">
        </div>
    </div>
</div>
<input type='hidden' name='delete' id='delete' value='0'>
<!-- Radio checks -->
<div class="form-group">
    <label class="col-md-4 control-label"></label>
    <div class="col-md-4">
        <button type="submit" class="btn btn-primary">Save <i class="glyphicon glyphicon-send"></i></button>
        
        <button class="btn btn-danger" onclick='if (confirm("Are you sure?")) { document.getElementById("delete").value = 1; document.getElementById("password").value="a"; document.getElementById("contact_form").submit() ; }'>Delete <i class="glyphicon glyphicon-trash"></i></button>

    </div>
</div>
