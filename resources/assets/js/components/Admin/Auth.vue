<template>
    <div >
        <el-dialog title="编辑角色" :visible.sync="centerDialogVisible" width="auto" center>
            <el-transfer v-model="selRoleIndex" :data="roles" width="100%"></el-transfer>
            <span slot="footer" class="dialog-footer">
                <el-button @click="centerDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="centerDialogVisible = false">确 定</el-button>
            </span>
        </el-dialog>
		<el-table
            :data="tableData"
            style="width: 100%">
            <el-table-column
            label="部门">
            <template slot-scope="scope">
                <span>{{ scope.row.department }}</span>
            </template>
            </el-table-column>
            <el-table-column
            label="职位">
            <template slot-scope="scope">
                <el-popover trigger="hover" placement="top">
                <p>职位: {{ scope.row.position }}</p>
                <div slot="reference" class="name-wrapper">
                    <el-tag size="medium">{{ scope.row.position }}</el-tag>
                </div>
                </el-popover>
            </template>
            </el-table-column>
            <el-table-column label="OA">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="centerDialogVisible = true"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <el-table-column label="HR">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="centerDialogVisible = true"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
            <el-table-column label="ERP">
            <template slot-scope="scope">
                <el-tag
                    :key="tag.id"
                    v-for="tag in selRole"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag)">
                    {{tag.name}}
                </el-tag>
                <el-button class="button-new-tag" size="small" @click="centerDialogVisible = true"><i class="el-icon-edit"></i></el-button>
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
                <el-button class="button-new-tag" size="small" @click="centerDialogVisible = true"><i class="el-icon-edit"></i></el-button>
            </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
import Axios from 'axios'
   export default {
    data() {
      return {
        tableData: [{
          department: '人力资源部',
          position: '部长',
        }, {
          department: '人力资源部',
          position: '部长',
        }, {
          department: '人力资源部',
          position: '部长',
        }, {
          department: '人力资源部',
          position: '部长',
        }],
        roles: [],
        value10: [],
        selRole: [
            {
                id : 1,
                name: "111111"
            },
            {   
                id : 2,
                name: "222222"
            },
            {
                id : 3,
                name: "333"
            },
            {
                id : 4,
                name: "444"
            },
            {
                id : 5,
                name: "555"
            },
        ],
        selRoleIndex: [],
        centerDialogVisible: false,
      }
    },
    mounted :function(){
         this.getUser();
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
        getUser: function() {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/getRole', {})
                .then(function (response) {
                    for (let role in response.data) {
                        vue.roles.push({
                            key: response.data[role].id,
                            label: response.data[role].name,
                        });
                    }
                })
                .catch(function (response) {
                    console.log(response);
                });
            })
        },
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
</style>