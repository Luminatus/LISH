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

    $('form#admin-form').submit((e)=>{
        console.log(e);        
        clearErrors();
        e.preventDefault();
        let form = $('form#admin-form');
        let pw = form.find('input[name=admin-pw]').val();        
        if(!pw)
            addError("Enter password!");
        
        console.log("haladunk");
         if(errors.length == 0)
         {
            console.log("ugye?");
             setLoading(true);
             //Send AJAX request
             $.ajax({
                url: BASEPATH + '/api/list_all',
                type: 'POST',
                data: { pw: pw },
                dataType: 'json'
            })
            .done( msg=>{
                console.log('krály', msg);
                headers = [{name: "Full url", width: 8}, {name: "Code", width: 4}];
                let tableElem = $('<div id="result-table" class="col-xs-12">');
                let tabHeader = $('<div class="row header-row">');
                for(let i in headers)
                {
                    console.log(headers[i]);
                    let headerElem = $('<div class="header-cell">');
                    headerElem.addClass('col-xs-'+headers[i].width);
                    headerElem.html(headers[i].name);
                    tabHeader.append(headerElem);
                }
                console.log(tabHeader);
                tableElem.append(tabHeader);

                for(let i in msg)
                {
                    let row = msg[i];
                    let rowElem = $('<div class="row result-row">');
                    rowElem.append('<div class="col-xs-8 result-cell">'+row.full_url+'</div>');
                    rowElem.append('<div class="col-xs-4 result-cell">'+row.short_url+'</div>');
                    tableElem.append(rowElem);
                }
                resultContainer.append(tableElem);
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
            $('form#admin-form').hide();
            resultContainer.show();
            loading.hide();
            submitButton.hide();
        }

    }
})