<?php
$myIdx=(isset($_GET['idx']))?trim($_GET['idx']):'';
?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myChildModalLabel">选择命令</h4>
        </div>
        <div class="modal-body" style="overflow:auto;" id="myChildModalBody">
          <div class="form-group">
            <label for="action_name" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
              <select class="form-control" id="action_name" onchange="listActionParams()">
                <option value="">请选择</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="action_param" class="col-sm-2 control-label">参数</label>
            <div class="col-sm-10 profile_details">
              <div class="well profile_view col-sm-12">
                <div class="col-sm-12">
                  <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                      <th>参数名</th>
                      <th>值类型</th>
                    </tr>
                    </thead>
                    <tbody id="action_param">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">取消</button>
            <button class="btn btn-success" data-dismiss="modal" id="btnCommitAction" onclick="setAction('<?php echo $myIdx;?>')" style="margin-bottom: 5px;" disabled>确认</button>
        </div>
        <script>
          getAction('<?php echo $myIdx;?>');
        </script>
