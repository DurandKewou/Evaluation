@extends('layout/admin-layout')

@section('space-work')

    <h2 class="mb-4">Marks</h2>

    <table class="table mt-5">
        <thead>
            <th>#</th>
            <th>Exam Name</th>
            <th>Marks/Q</th>
            <th>Total Marks</th>
            <th>Passing Marks</th>
            <th>Edit</th>
        </thead>
        <tbody>
            @if (count($exams)>0)
            @php
                $x = 1;
            @endphp
                @foreach ($exams as $exam)
                    <tr>
                        <td>{{$x++}}</td>
                        <td>{{$exam->exam_name}}</td>
                        <td>{{$exam->marks}}</td>
                        <td>{{count($exam->getQnaExam)*$exam->marks }}</td>
                        <td>{{$exam->pass_marks}}</td>
                        <td>
                            <button class="btn btn-primary editMarks" data-id="{{$exam->id}}" data-pass-marks="{{$exam->pass_marks}}" data-marks="{{$exam->marks}}" data-totalq="{{count($exam->getQnaExam)}}" data-bs-toggle="modal" data-bs-target="#editMarksModal">Edit</button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">Exam not added!</td>
                </tr>
            @endif
        </tbody>
    </table>

        <!--Update Marks Modal -->
        <div class="modal fade" id="editMarksModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Marks</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editMarks">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="">Marks/Q</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="hidden" name="exam_id" id="exam_id">
                                    <input type="text" onkeypress="return event.charCode>= 48 && event.charCode<=57 || event.charCode == 46" name="marks" id="marks" placeholder="Enter Marks/Q" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <label for="">Total Marks</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="tmarks" disabled id="tmarks" placeholder="Total Marks" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <label for="">Passing Marks</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" onkeypress="return event.charCode>= 48 && event.charCode<=57 || event.charCode == 46" name="pass_marks" id="pass_marks" placeholder="Enter Passing Marks" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Marks</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>
        $(document).ready(function(){
            var totalQna = 0;
            $('.editMarks').click(function(){
                var exam_id = $(this).attr('data-id');
                var marks = $(this).attr('data-marks');
                var totalq = $(this).attr('data-totalq');

                $('#marks').val(marks);
                $('#exam_id').val(exam_id);
                $('#tmarks').val((marks*totalq).toFixed(1));

                totalQna = totalq;
                
                $('#pass_marks').val($(this).attr('data-pass-marks'));
            });

            $('#marks').keyup(function(){
                $('#tmarks').val(($(this).val() * totalQna).toFixed(1));
            });

            $('#pass_marks').keyup(function(){
                $('.pass-error').remove();
                var tmarks = $('#tmarks').val();
                var pmarks = $(this).val();
                if (parseFloat(pmarks) >= parseFloat(tmarks)) {
                    $(this).parent().append('<p style="color:red;" class="pass-error">Passing Marks will be less than total marks!</p>');
                    setTimeout(() => {
                        $('.pass-error').remove();
                    }, 2000);
                }
            });

            $('#editMarks').submit(function(event){
                event.preventDefault();

                $('.pass-error').remove();
                var tmarks = $('#tmarks').val();
                var pmarks = $('#pass_marks').val();
                if (parseFloat(pmarks) >= parseFloat(tmarks)) {
                    $('#pass_marks').parent().append('<p style="color:red;" class="pass-error">Passing Marks will be less than total marks!</p>');
                    setTimeout(() => {
                        $('.pass-error').remove();
                    }, 2000);

                    return false;
                }

                var formData = $(this).serialize();

                $.ajax({
                    url:"{{ route('updateMarks') }}",
                    type:"POST",
                    data:formData,
                    success:function(data){
                        if (data.success == true) {
                            location.reload();
                        }else{
                            alert(data.msg);
                        }
                    }
                });
            });
        });
    </script>

@endsection