const styleFileUpload = `<style>.file--upload-inner .file--upload { width: 100%; padding: 3rem 2rem; border-radius: 15px; border: 1px dashed rgb(26, 183, 227); margin-bottom: 10px; transition: all .3s linear; background: #fff; position: relative; overflow: hidden; } .file--upload-inner .file--upload .file--upload-msg { font-size: 12px; } .file--upload.disabled::after { content: ''; display: block; width: 100%; height: 100%; background: rgba(255, 255, 255, .8); position: absolute; top: 0; left: 0; } .file--upload.drag { border-color: var(--bs-orange); background: #fff9f4; } .file--upload.drag .file--upload-icon svg { fill: rgb(255,211,174); } .file--upload.error { border-color: var(--bs-danger); background: #fff9f4; } .file--upload.error .file--upload-icon svg { fill: rgb(255,211,174); } .file--upload.drop { border-color: var(--bs-teal); background: #e9fff8; } .file--upload.drop .file--upload-icon svg { fill: #5ed6b3; } .file--upload-icon { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto; margin-bottom: 10px; } .file--upload-icon svg { fill: rgb(210,210,210); transition: all .3s linear; } .file--upload-inner .file-upload--items { width: 100%; border-radius: 8px; border: 1px solid #ddd; display: flex; align-items: center; padding: .8rem; margin-bottom: 10px; } .file--upload-inner .file-upload--items .file-upload--item-remove-btn { width: 25px; margin-left: auto; color: #f1f1f1; cursor: pointer; } .file--upload-inner .file-upload--items .file-upload--items-title { flex: 1; font-size: 14px; display: block; } .file--upload-inner .file-upload--items .file-upload--item-remove-btn:hover svg { fill: #cd7b7b; } .file--upload-inner .file-upload--items .file-upload--item-remove-btn svg { fill: #e89b9b; transition: all .3s linear; } </style>`;
$('head').append(styleFileUpload)

let fileAcceptTypes = {
    "gif" 	: "image/gif",
    "jpg" 	: "image/jpeg",
    "jpeg" 	: "image/jpeg",
    "png" 	: "image/png",
    "xlsx" 	: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "xls" 	: "application/vnd.ms-excel",
    "pdf" 	: "application/pdf",
    "docx" 	: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "doc" 	: "application/msword",
    "geojson" 	: "application/json",
};

function getAcceptTypeFile(param)
{
    return fileAcceptTypes[param];
}

class FileUpload {
    constructor(selector,config)
    {
        this.selector = selector
        this.config = config
        this.selectorDataContent;
        this.selectorUploadContentItemIds;
        this.draw()
        return this
    }
    draw()
    {
        let that = this;


        let MyConfig = that.config

        let max_size = MyConfig.maxSize? parseInt(MyConfig.maxSize) : 5;
        let max_file = MyConfig.maxFile? parseInt(MyConfig.maxFile) : 10;
        let file_accept = MyConfig.accept.length? MyConfig.accept : [
            'xlsx',
            'xls'
        ]

        let acceptFileTypes = [],
        accept_type_text = [];

        $.each(MyConfig.accept,function(i,key){
            acceptFileTypes.push(getAcceptTypeFile(key))
            accept_type_text.push('.'+key)
        })
        let result_acceptFileTypes = acceptFileTypes.join(',')
        let result_accept_type_text = accept_type_text.join(',')

        let maxSizeConvert = max_size*1000000

        let msgContent = ''

        if((result_accept_type_text != '') && (max_size != ''))
        {
            msgContent = `<b class="text-warning">📒Note: </b> File harus berformat ${result_accept_type_text}, dan maksimal ukuran ${max_size}mb.`
        }
        if((result_accept_type_text != '') && (max_size == ''))
        {
            msgContent = `<b class="text-warning">📒Note: </b> File harus berformat ${result_accept_type_text}.`
        }
        if((result_accept_type_text == '') && (max_size != ''))
        {
            msgContent = `<b class="text-warning">📒Note: </b> maksimal ukuran ${max_size}mb💢.`
        }
        let maxFileMsg = ''
        if(max_file)
        {
            maxFileMsg = `<div class="highlight-overlay"><small>Maksimal: ${max_file} gambar</small></div>`
        }

        let uploadContentIds = randId(11),
            inputIds = randId(8),
            btnIds = randId(4),
            uploadContentItemIds = randId(9),
            dataContentItem = uploadContentItemIds+'_data',
            uploadErrorContentIds = uploadContentItemIds+'_errors';

        this.selectorDataContent = dataContentItem
        this.selectorUploadContentItemIds = uploadContentItemIds

        let content = `<div class="file--upload-inner">
        <div class="file--upload text-center user-select-none ${uploadContentIds}">
                            <div class="file--upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M152 120c-26.51 0-48 21.49-48 48s21.49 48 48 48s48-21.49 48-48S178.5 120 152 120zM447.1 32h-384C28.65 32-.0091 60.65-.0091 96v320c0 35.35 28.65 64 63.1 64h384c35.35 0 64-28.65 64-64V96C511.1 60.65 483.3 32 447.1 32zM463.1 409.3l-136.8-185.9C323.8 218.8 318.1 216 312 216c-6.113 0-11.82 2.768-15.21 7.379l-106.6 144.1l-37.09-46.1c-3.441-4.279-8.934-6.809-14.77-6.809c-5.842 0-11.33 2.529-14.78 6.809l-75.52 93.81c0-.0293 0 .0293 0 0L47.99 96c0-8.822 7.178-16 16-16h384c8.822 0 16 7.178 16 16V409.3z"/></svg>
                            </div>
                            <h5 class="text-muted" style="font-size: 14px;">Seret dan lepas, atau <span class="text-primary fw-600 cursor-pointer ${btnIds}">Jelajahi🔎</span> file anda.</h5>
                            <i class="file--upload-msg">${msgContent}</i>
                        </div>
                        <div class="${uploadContentItemIds}"></div>
                        <div class="${uploadErrorContentIds} text-danger"></div>
                        </div>`

        $('body').append(
            `<div class="d-none">
                <input type="file" class="d-none ${inputIds}" ${acceptFileTypes.length > 0? 'accept="'+result_acceptFileTypes+'"' : ''} multiple hidden>
                <div class="${dataContentItem}">
                </div>
            </div>`
        )


        $(document).on('click','.'+btnIds, function(e){
            $('.'+inputIds).trigger('click')
        })

        function renderUploadFile(files)
        {

            $('.'+uploadErrorContentIds).html('')
            $('.'+uploadContentIds).addClass('drop')

            let contentList = $('.'+uploadContentItemIds),
            dataContentList = $('.'+dataContentItem)

            let errorContentMsg = ''

            let imgItemExists = $('.'+uploadContentItemIds+' .file-upload--items').length
            if(imgItemExists+files.length > max_file)
            {
                $('.'+uploadContentIds).addClass('error')
                setTimeout(function(){
                    $('.'+uploadContentIds).removeClass('drop')
                    .removeClass('drag')
                    .removeClass('error')
                },1000)
                errorContentMsg += `<span class="d-block animation-fadeIn">● File yang diunggah melebihi ${max_file} ❗</span>`
            }

            if(files.length && (errorContentMsg == ''))
            {
                let filesIndex = 1;
                $.each(files,function(e,file){
                    let fileType = file.type
                    let fileSize = file.size
                    let fileName = file.name

                    let uploadItemIds = randId(5)+'_'+randId(10),
                        btnRemoveItemIds = uploadItemIds+'_remove',
                        inputItemIds = uploadItemIds+'_data';


                    if(!acceptFileTypes.includes(fileType))
                    {
                        errorContentMsg += `<span class="d-block animation-fadeIn">● ${fileName} format tidak sesuai ❗</span>`
                    }

                    if(acceptFileTypes.includes(fileType) && (fileSize <= maxSizeConvert))
                    {

                        var reader = new FileReader();

                        reader.addEventListener("load", function(e) {

                            let resultData = e.target.result

                            contentList.append(`<div class="file-upload--items user-select-none ${uploadItemIds}">
                                                    <span class="file-upload--items-title text-muted">
                                                        ${fileName}
                                                        </span>
                                                        <div class="file-upload--item-remove-btn ${btnRemoveItemIds}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256zM175 208.1L222.1 255.1L175 303C165.7 312.4 165.7 327.6 175 336.1C184.4 346.3 199.6 346.3 208.1 336.1L255.1 289.9L303 336.1C312.4 346.3 327.6 346.3 336.1 336.1C346.3 327.6 346.3 312.4 336.1 303L289.9 255.1L336.1 208.1C346.3 199.6 346.3 184.4 336.1 175C327.6 165.7 312.4 165.7 303 175L255.1 222.1L208.1 175C199.6 165.7 184.4 165.7 175 175C165.7 184.4 165.7 199.6 175 208.1V208.1z"/></svg>
                                                        </div>
                                                </div>`)

                            dataContentList.append(`<input type="text" class="upload-item-data ${inputItemIds}" value="${resultData}">`)

                        });

                        reader.readAsDataURL(file);
                        if(files.length == filesIndex)
                        {
                            $('.'+inputIds).val('')
                        }
                        $(document).on('click','.'+btnRemoveItemIds,function(e){
                            e.preventDefault()
                            $('.'+uploadItemIds).remove()
                            $('.'+dataContentItem+' .'+inputItemIds).remove()
                            that.checkItem()
                        })

                    }

                    filesIndex++;

                })
            }

            if(errorContentMsg != '')
            {
                $('.'+uploadErrorContentIds).html(errorContentMsg)
            }

            $('.'+uploadContentIds).removeClass('drag')
            setTimeout(function(){
                if($('.'+uploadContentItemIds+' .file-upload--items').length == max_file)
                {
                    $('.'+uploadContentIds).addClass('disabled')
                }
                $('.'+uploadContentIds).removeClass('drop')
            },300)

        }

        $(document).on('change','.'+inputIds, function(e){
            var files = e.target.files;
            renderUploadFile(files)
        })

        $(document).on('dragover','.'+uploadContentIds+':not(.disabled)',function(e){
            e.preventDefault()
            $(this).addClass('drag')
        })

        $(document).on('dragleave','.'+uploadContentIds+':not(.disabled)',function(e){
            e.preventDefault()
            $(this).removeClass('drag')
        })

        $(document).on('drop', '.'+uploadContentIds+':not(.disabled)', function(e) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;

            renderUploadFile(files)
        });

        $(this.selector).html(content)

    }
    checkItem()
    {
        let that = this
        if($(that.selector+' .file-upload--items').length >= that.config.maxFile)
        {
            $(that.selector).find('.file--upload').addClass('disabled')
        }
        if($(that.selector+' .file-upload--items').length < that.config.maxFile)
        {
            $(that.selector).find('.file--upload').removeClass('disabled')
        }
    }
    getFiles()
    {
        let dataFile = []
        $('.'+this.selectorDataContent+' .upload-item-data').each(function(i,el){
            let that = $(el)
            let myFile = that.val()
            if(myFile)
            {
                dataFile.push(myFile)
            }
        })
        if(this.config.maxFile == 1)
        {
            return dataFile.length? dataFile[0] : null;
        }
        return dataFile;
    }
    reset()
    {
        $('.'+this.selectorDataContent).html('')
        $('.'+this.selectorUploadContentItemIds).html('')
        this.checkItem()
    }
}
