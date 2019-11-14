<fieldset>
    <div class="form-group">
        <label for="task_name">Task Name *</label>
          <input type="text" name="task_name" value="<?php echo htmlspecialchars($edit ? $customer['task_name'] : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Task Name" class="form-control" required="required" id = "task_name">
    </div> 

    <div class="form-group">
        <label for="start_date">Start date *</label>
        <input type="text" name="start_date" value="<?php echo htmlspecialchars($edit ? $customer['start_date'] : date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Start date" class="form-control" required="required" id="start_date">
    </div> 

     <div class="form-group">
        <label for="end_date">End date</label>
        <input type="text" name="end_date" value="<?php echo htmlspecialchars($edit ? $customer['end_date'] : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="End date" class="form-control" id="end_date">
    </div>    

    <div class="form-group">
        <label for="address">Comment</label>
          <textarea name="comment" placeholder="Comment" class="form-control" id="comment"><?php echo htmlspecialchars(($edit) ? $customer['comment'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    
    <div class="form-group text-center">
        <label></label>
        <button type="submit" class="btn btn-warning" >Save <i class="glyphicon glyphicon-send"></i></button>
    </div>
</fieldset>
