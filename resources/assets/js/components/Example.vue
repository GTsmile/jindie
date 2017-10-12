<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>
                    <el-radio class="radio" v-model="radio" label="1">备选项</el-radio>
                    <div class="panel-body">
                        I'm an example component!666
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
          return {
            radio: '1'
          }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
