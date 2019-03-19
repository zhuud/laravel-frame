<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">{{ title }}</div>
                    <div class="card-body">
                        <div  class="input-group">
                            <input type="text" class="form-control" placeholder="小鲸鱼 我喜欢你 ( ´◔ ‸◔')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('this is my first vue')
        },
        data() {
            return  {
                title: 'hello world',
            }
        },
        filters: {
            searchFor: function(value, searchString) {
            }
        },
        methods: {
            searchFor: function(value, searchString) {
            }
        }
    }

</script>
