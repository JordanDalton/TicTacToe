<script>
    export default {

        methods : {

            superAxios(url_object, data, config = {}, response, error)
            {
                return window.axios[url_object.axios_method](url_object.url, data, config)
                             .then(response ? response : (response)=>{

                             }, error ? error : (error)=>{

                             })
            }

        }

    }
</script>