<div class="modal fade" id="tags-modal" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header d-flex align-items-center justify-content-between px-4 py-2">

                <div class="d-flex">
                    <h4 class="modal-title">Manage Tags</h4>
                
                    <i style="font-size: 16px; margin-left: 15px; margin-top: 10px;" class="fas fa-question-circle" data-toggle="tooltip" data-placement="bottom" data-html="true"
                    title="You can click a tag to edit the name of it or delete it. The system will not let you create <i>similar</i> tags such as <b>'Dog'</b> and <b>'dog'</b>."></i>
                </div>
 
                @if(Auth::user()->username == 'admin')
                    <a class="btn btn-sm btn-secondary" href="/pilot/merge-tags"><i class="fas fa-code-merge"></i></i> Merge Tags</a>
                @endif
 
                <button type="button" class="close float-none m-0 p-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>

            <div class="modal-body">

                <div class="simple-create">
                    <input type="text" data-endpoint="{{ route('admin.tag.store') }}">
                    <button type="button" class="btn btn-primary btn-sm">Add Tag</button>
                </div>

                <div class="simple-create-list">

                    @foreach($tags as $id => $tag)

                        <p id="editable-tag-{{ $tag->id }}" class="inline-editable-container">
                            <span class="inline-editable">{{ $tag->name }}</span>
                            <span class="inline-editor" style="display: none;"><input type="text" value="{{ $tag->name }}" data-id="{{$tag->id}}" data-endpoint="{{ route('admin.tag.update', array('tag' => $tag->id)) }}">
                                <br>
                                <button type="button" class="inline-editable-save btn btn-primary btn-sm">Save</button>
                                <button type="button" class="inline-editable-cancel btn-secondary btn-sm">Cancel</button>
                                <button type="button" class="inline-editable-delete btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTagModal">Delete</button></span>

                        </p>

                    @endforeach

                </div>

            </div>

                            <!-- The Modal -->
                <div class="modal fade" id="deleteTagModal">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                        <h3 class="modal-title">Danger Zone! </h3>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                                If you delete a tag, every single News Post, Event, Resource, etc, will be detached from the Tag. <br><br>For example, if you delete a tag named "Dog",
                                the page https://www.mywebsite.org/news/tagged/14/dog will no longer exist on your website because there is not a tag called Dog in the system
                                anymore.<br><br>

                                Actually the tag and it's relationships get "soft deleted", which means that FLEX360 can bring it back,
                                but if you are unsure of what things will change on your website from deleting a tag, we recommended getting in touch
                                with us so we can help you. <br><br>

                                If you are sure, type the name of the tag below, check the box, and click Delete.<br><br>


                                    <input class="form-control" id="tagInputField" type="text" name="areYouSure" placeholder="Type the name of the tag to confirm">
                                    <br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="areYouSureCheckbox">
                                        <label class="form-check-label" for="exampleCheck1">Are you sure?</label>
                                    </div>
                                    <br>
                                    <button disabled type="submit" class="btn btn-danger disabled" id="perma-delete-btn" style="width: 100%;">Delete</button>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>

                    </div>
                    </div>
                </div>

            <?php /* <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div> */ ;?>

        </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->

</div><!-- /.modal -->


@section('scripts')
<script>

        $(document).ready(function() {

            //Global variables
            //Get Button
            const permaDeleteBtn = document.getElementById("perma-delete-btn");
            //Get input field
            const tagInputField = document.getElementById("tagInputField");
            //Get checkbox
            const tagCheckbox = document.getElementById("areYouSureCheckbox");


            //Show modal if delete button is pressed and set the Tag name and it's Id that it was pressed on to a variable
            $('#deleteTagModal').on('show.bs.modal', function (e) {
            //Get string of tag name, and its data-id attribute value
            var tagName = e.relatedTarget.closest('.inline-editable-container').querySelector('.inline-editable').textContent;
            var tagDataId = e.relatedTarget.closest('.inline-editable-container').querySelector('.inline-editor').firstChild.getAttribute("data-id");
                //If the input's value is the same string as the tagName AND checkbox is true,
                //then remove the disabled class of perma delete btn so user can press it
                $('#areYouSureCheckbox').change(function(){
                    if(tagInputField.value == tagName && tagCheckbox.checked == true) {
                            permaDeleteBtn.classList.remove('disabled');
                            permaDeleteBtn.removeAttribute("disabled")
                        }
                        else {
                            permaDeleteBtn.classList.add('disabled');
                            permaDeleteBtn.disabled = true;
                        }
                });

                //On click on perma delete button
                //Send ajax request to delete the tag from the database
                $('#perma-delete-btn').unbind('click').on('click', function (e) {
                    $.post('/pilot/tag/' + tagDataId, { _method: 'delete', _token: $('[name=csrf-token]').attr('content') }, function(result) {
                        document.querySelector('#editable-tag-'+ tagDataId).remove();
                        $('#deleteTagModal').modal('hide');
                    });
                })

            });

            //If user clicks out of the modal, remove any text from input field
            //and uncheck the "I Am Sure" checkbox
            $('#deleteTagModal').on('hidden.bs.modal', function (e) {
                permaDeleteBtn.classList.add('disabled');
                permaDeleteBtn.disabled = true;
                tagInputField.value = '';
                tagCheckbox.checked = false;
            })

            //If original Tag modal is closed, make sure to display: none of inline editable
            //or the Save, Cancel, and Delete buttons will still be showing when you
            //click Manage tags button again
            $('#tags-modal').on('hidden.bs.modal', function (e) {
                var inlineEditables = document.getElementsByClassName("inline-editable");
                for(i = 0; i < inlineEditables.length; i++) {
                    inlineEditables[i].removeAttribute('style');
                }
                var inlineEditors = document.getElementsByClassName("inline-editor");
                for(i = 0; i < inlineEditors.length; i++) {
                    inlineEditors[i].style.display = 'none';
                }

            })
        })

</script>
@endsection
