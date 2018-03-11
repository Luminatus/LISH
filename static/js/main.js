$('document').ready(()=>{
    let loading = $('img#loading');
    let submitButton = $('button#submit-button');
    let errorContainer = $('div#error-container');
    let resultContainer = $('div#result-container');
    let BASEPATH = $('meta[name=basepath]').attr("content");
    let isloading = false;
    let issuccess = false;
    let errors = [];

    console.log(BASEPATH);
    setLoading(false)

    $('form#link-form').submit((e)=>{
        console.log(e);        
        clearErrors();
        e.preventDefault();
        let params = {};
        let form = $('form#link-form');
        let url = form.find('input[name=url]').val();
        let code = form.find('input[name=code]').val();
        console.log(form);
        console.log(url,code);
        if(!url)
            addError("URL is missing!");
        if(!code)
            addError("Code is missing!");
        else if(!(/^[a-z0-9_]*[a-z0-9][a-z0-9_]*$/gi.test(code)))
        {
            let errorMessage = "Code is not in correct format. Code may only contain english letters, number, and the _ symbol, and must contain at least one letter or number!";
            addError(errorMessage);
         }

         if(errors.length == 0)
         {
             setLoading(true);
             //Send AJAX request
             $.ajax({
                url: BASEPATH + '/api/create',
                type: 'POST',
                data: { full_url: url, short_url: code },
                dataType: 'json'
            })
            .done( msg=>{
                console.log('krály', msg);
                let link =BASEPATH+"/"+msg.short_url;
                let linkElem = $('a#result-link')
                linkElem.html(link);
                linkElem.attr('href', link);
                issuccess = true;
                setLoading(false);
            })
            .fail(err =>{
                console.log('gáz', err);
                setLoading(false);
                let response = err.responseText;
                if(response)
                {
                    response = JSON.parse(response);
                    if(response instanceof Array)
                    {
                        for(let i in response)
                        {
                            let error = response[i];
                            addError(error);
                        }
                    }
                    else
                    {
                        addError(response);
                    }
                }
                else
                {
                    addError("An unknown error occured");
                }
            })
         }
    });

    function addError(msg)
    {
           let elem = $('<p class="alert-danger">');
            elem.html(msg);         
            errors.push(msg);   
            errorContainer.append(elem);        
    }

    function clearErrors()
    {
        errors = [];
        errorContainer.html('');
    }

    function setLoading(val)
    {
        if(!issuccess)
        {
            resultContainer.hide();
            isloading = val;
            if(isloading)
            {
                submitButton.hide();
                loading.show();
            }
            else
            {
                loading.hide();
                submitButton.show();
            }
        }
        else
        {
            resultContainer.show();
            loading.hide();
            submitButton.hide();
        }

    }
})