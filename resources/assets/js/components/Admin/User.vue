<template>
    <div >
		  <el-table :data="tableData" stripe style="width: 100%">
        <el-table-column
          prop="name"
          label="姓名">
        </el-table-column>
        <el-table-column
          prop="position"
          label="职位">
        </el-table-column>
        <el-table-column
          prop="department"
          label="部门">
        </el-table-column>
      </el-table>
    </div>
</template>

<script>
import Axios from 'axios'
   export default {
    data() {
      return {
        tableData: []
      }
    },
     methods: {
        handleEdit(index, row) {
          console.log(index, row);
        },
        handleDelete(index, row) {
          console.log(index, row);
        },
        getUser: function() {
            var vue=this;
            this.$nextTick(function () {
                axios.post('/getUser', {})
                .then(function (response) {
                   vue.tableData=response.data;
                   console.log(vue.tableData);
                })
                .catch(function (response) {
                    console.log(response);
                });
            })
        },
     },
     mounted: function(){
       this.getUser();
     }
    }
</script>

<style scoped>

</style>