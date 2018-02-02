<template>
    <div >
        <br/>
        <el-form :inline="true">
            <el-form-item>
                <el-input  placeholder="请输入....." v-model="inputSearch"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="search" @click="getUser(currentPage,pageSize,inputSearch,orderName,order)">查询</el-button>
            </el-form-item>
            <el-form-item style="float:right">
                <el-button type="primary"  @click="getUser(currentPage,pageSize,inputSearch,orderName,order)">导出</el-button>
            </el-form-item>
        </el-form>
		  <el-table :data="tableData" stripe style="width: 100%" 
      @sort-change="sortChange"
      >
        <el-table-column
          prop="name"
          label="姓名"
          sortable='custom'>
        </el-table-column>
        <el-table-column
          prop="position"
          label="职位"
          sortable='custom'>
        </el-table-column>
        <el-table-column
          prop="department"
          label="部门"
          sortable='custom'>
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
        tableData: [],
        currentPage: 1,
        pageSize: 10,
        total:0,
        inputSearch: "", //table搜索框
        orderName: "",
        order: "",
      }
    },
     methods: {
        handleEdit(index, row) {
          console.log(index, row);
        },
        handleDelete(index, row) {
          console.log(index, row);
        },
        handleSizeChange(val) {
          console.log(`每页 ${val} 条`);
          this.getUser(this.currentPage,val,this.inputSearch,this.orderName,this.order); 
        },
        handleCurrentChange(val) {
          console.log(`当前页: ${val}`);
          this.getUser(val,this.pageSize,this.inputSearch,this.orderName,this.order); 
        },
        getUser: function($currentPage,$pageSize,$inputSearch,$orderName,$order) {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/getUser', {
                  'currentPage': $currentPage,
                  'pageSize': $pageSize,
                  'where' : $inputSearch,
                  'orderName' : $orderName,
                  'order' : $order
                })
                .then(function (response) {
                   vue.tableData=response.data.select_row;
                   vue.total=response.data.userCount;
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
              axios.post('/getUser', {
                'currentPage': vue.currentPage,
                'pageSize': vue.pageSize,
                'where' : vue.inputSearch,
                'orderName' : prop,
                'order' : order
              })
              .then(function (response) {
                  vue.tableData=response.data.select_row;
                  vue.total=response.data.userCount;
              })
              .catch(function (response) {
                  console.log(response);
              });
          })
        }
     },
     mounted: function(){
       this.getUser(1,10);
     }
    }
</script>

<style scoped>
.block{
  padding-top: 10px;
  text-align: center;
}
</style>