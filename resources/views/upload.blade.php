<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Upload Archive</title>

  {{--  Bootstrap connect--}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card">
          <div class="card-header">
            Archive Upload
          </div>
          <div class="card-body">
            <form method="post" action="{{route('upload.uploadfile')}}" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <label for="file">Choose only "*.zip" file</label>
                <input type="file" class="form-control" name="file" id="file"/>
              </div>
              <button type="submit" class="btn btn-success">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
