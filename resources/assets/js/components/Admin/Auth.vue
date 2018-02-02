<template>
    <div >
        <el-dialog title="编辑角色" :visible.sync="centerDialogVisible" width="auto" center>
            <el-transfer filterable v-model="selRoleIndex" :data="roles" width="100%" :titles="['可选角色','已选角色']"></el-transfer>
            <span slot="footer" class="dialog-footer">
                <el-button @click="centerDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="handleChange();">确 定</el-button>
            </span>
        </el-dialog>
        <br/>
        <el-form :inline="true">
            <el-form-item>
                <el-input  placeholder="请输入....." v-model="inputSearch"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="search" @click="getPositionUnit(currentPage,pageSize,inputSearch,orderName,order)">查询</el-button>
            </el-form-item>
        </el-form>
		<el-table @sort-change="sortChange"
            :data="tableData"
            style="width: 100%"
            @expand="handleCurrentc()">
            <el-table-column
            label="部门" 
            prop="HR_position_departant"
            sortable='custom'>
            <template slot-scope="scope">
                <span>{{ scope.row.HR_position_departant }}</span>
            </template>
            </el-table-column>
            <el-table-column
            label="职位" 
            prop="HR_position_name"
            sortable='custom'>
            <template slot-scope="scope">
                <el-popover trigger="hover" placement="top">
                <p :data="scope.row.id">职位: {{ scope.row.HR_position_name }}</p>
                <div slot="reference" class="name-wrapper">
                    <el-tag size="medium">{{ scope.row.HR_position_name }}</el-tag>
                </div>
                </el-popover>
            </template>
            </el-table-column>
            <el-table-column label="OA">
            <template slot-scope="scope">
                    <span :key="index1" v-for="(t,index1) in roleOA">
                        <el-tag
                        :disable-transitions="false"
                        v-if="tag == t.key"
                        :key="index" v-for="(tag,index)  in scope.row.oa_role_id"
                        >
                        {{ roleOA[index1].label }}
                        </el-tag>
                    </span>
                <el-button class="button-new-tag" size="small" @click="modelShow(roleOA,scope,scope.row.oa_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <el-table-column label="HR">
            <template slot-scope="scope">
                    <el-tag
                        v-if="scope.row.hr_role_id != 0 "
                        :disable-transitions="false">
                        {{ scope.row.hr_role_id.length }}
                        个权限
                    </el-tag>
                <el-button class="button-new-tag" size="small" @click="modelShow(roleHR,scope,scope.row.hr_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <el-table-column label="ERP">
            <template slot-scope="scope">
                <el-tag :disable-transitions="false" v-if="scope.row.erp_role_id">
                    {{ scope.row.erp_role_id.length }}
                    个权限
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="modelShow(roleERP,scope,scope.row.erp_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <!-- <el-table-column label="CRM">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="modelShow(roleCRM,scope,scope.row.crm_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column> -->
            <el-table-column label="SCM">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="modelShow(roleSCM,scope,scope.row.scm_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <el-table-column label="PLM">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="modelShow(rolePLM,scope,scope.row.plm_role_id)"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
        </el-table>
        <div class="block">
            <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="currentPage"
            :page-sizes="[10, 20, 30, 40]"
            :page-size="pageSize"
            layout="total, sizes, prev, pager, next, jumper"
            :total="total">
            </el-pagination>
        </div>
    </div>
</template>

<script>
import Axios from 'axios'
   export default {
    data() {
      return {
        tableData: [],      //table数据
        roles: [],          //当前弹出模态框显示数据（角色）
        value10: [],        
        inputSearch: "", //table搜索框
        selRole: [],        //每个数据库对应职位当前的角色信息
        currentDBName: 0, //当前点击的列
        currentPositionId:0,//当前点击的行的职位id
        currentPositionName:"",//当前点击的行的职位id
        currentDepartment:"",//当前点击的行的职位id
        selRoleIndex: [],   //当前穿梭框里选择的角色id
        centerDialogVisible: false, //是否弹出模态框
        currentPage: 1, //当前页（分页）
        pageSize: 10,   //每页的大小
        orderName: "",
        order: "",
        total:0,      //分页总条数
        roleOA:[],      //存储OA系统的角色数据
        roleHR:[],
        roleERP:[],
        roleCRM:[],
        roleSCM:[],
        rolePLM:[],
        roleOAIndex:[],      //存储OA系统的角色数据
        roleHRIndex:[],
        roleERPIndex:[],
        roleCRMIndex:[],
        roleSCMIndex:[],
        rolePLMIndex:[],
      }
    },
    mounted :function(){
         this.getRole();
         this.getPositionUnit(1,10);
    },
    methods: {
        handleEdit(index, row) {
            console.log(index, row);
        },
        handleDelete(index, row) {
            console.log(index, row);
        },
        handleClose(tag) {
            this.selRole.splice(this.selRole.indexOf(tag), 1);
        },
        handleSizeChange(val) {
            console.log(`每页 ${val} 条`);
            this.getPositionUnit(this.currentPage,val,this.inputSearch,this.orderName,this.order); 
        },
        handleCurrentChange(val) {
          console.log(`当前页: ${val}`);
          this.getPositionUnit(val,this.pageSize,this.inputSearch,this.orderName,this.order);
          this.currentPage=val;
        },
        handleCurrentc(row, expanded){
            console.log(row);
        },
        modelShow($currentRole,$scope,$currentRoleIndex) {
            this.roles=$currentRole;
            if($currentRoleIndex==null||$currentRoleIndex=="") this.selRoleIndex=[];
            else this.selRoleIndex=$currentRoleIndex;
            this.currentDBName=$scope.column.label;
            this.currentPositionId=$scope.row.id;
            this.currentPositionName=$scope.row.position;//当前点击的行的职位id
            this.currentDepartment=$scope.row.department;
            this.centerDialogVisible = true; 
        },
        handleChange() {
            this.centerDialogVisible = false;
            console.log(this.currentDBName);
            console.log(this.selRoleIndex);
            this.updateRole();
        },
        updateRole: function() {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/updateRole', {
                    'selRoleIndex': vue.selRoleIndex,
                    'db': vue.currentDBName,
                    'currentPositionId': vue.currentPositionId,
                    'currentPositionName': vue.currentPositionName,
                    'currentDepartment': vue.currentDepartment
                })
                .then(function (response) {
                    if(response.data=="true"){
                        vue.getPositionUnit(vue.currentPage,vue.pageSize,vue.inputSearch,vue.orderName,vue.order);
                    }
                })
                .catch(function (response) {
                    console.log(response.data);
                });
            })
        },
        getRole: function() {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/getRole', {})
                .then(function (response) {
                    for (let role in response.data.roleOA) {
                        vue.roleOA.push({
                            key: response.data.roleOA[role].id,
                            label: response.data.roleOA[role].name,
                        });
                    }
                    for (let role in response.data.roleERP) {
                        vue.roleERP.push({
                            key: response.data.roleERP[role].FGroupID,
                            label: response.data.roleERP[role].FSubSys+"->"+response.data.roleERP[role].FAccess,
                        });
                    }
                    for (let role in response.data.roleHR) {
                        vue.roleHR.push({
                            key: response.data.roleHR[role].ID,
                            label: response.data.roleHR[role].Name,
                        });
                    }
                })
                .catch(function (response) {
                    console.log(response.data);
                });
            })
        },
        getPositionUnit: function($currentPage,$pageSize,$inputSearch,$orderName,$order) {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/getPositionUnit', {
                    'currentPage': $currentPage,
                    'pageSize': $pageSize,
                    'where': $inputSearch,
                    'orderName' : $orderName,
                    'order' : $order
                })
                .then(function (response) {
                    vue.tableData=response.data.select_row;
                    vue.total=response.data.positionCount;
                })
                .catch(function (response) {
                    console.log(response);
                });
            })
        },
        sortChange({ column, prop, order }){
          var vue=this;
          vue.orderName=prop;
          vue.order=order;
            this.$nextTick(function () {
                axios.post('/getPositionUnit', {
                  'currentPage': vue.currentPage,
                  'pageSize': vue.pageSize,
                  'where' : vue.inputSearch,
                  'orderName' : prop,
                  'order' : order
                })
                .then(function (response) {
                    vue.tableData=response.data.select_row;
                    vue.total=response.data.positionCount;
                })
                .catch(function (response) {
                    console.log(response);
                });
            })
        }
      },
    }
</script>

<style scoped>
.el-tag + .el-tag {
    margin-left: 10px;
  }
.button-new-tag {
height: 32px;
padding-top: 0;
padding-bottom: 0;
width:100%;
margin-top: 2px;
margin-bottom: 2px;
}
.input-new-tag {
width: 90px;
margin-left: 10px;
vertical-align: bottom;
}
</style>
<style>
.el-dialog--small{
    width: auto;
}
.el-dialog{
    width:800px;
    top:10%;
}
.el-dialog__body{
    height: 400px;
}
.el-transfer-panel__list.is-filterable{
    height: 290px;
}
.el-dialog__wrapper{
    overflow: hidden;
}
.el-transfer-panel{
    width:350px;
    height: 400px;
}
.block{
  padding-top: 10px;
  text-align: center;
}
</style>