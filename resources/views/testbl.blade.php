<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TestBL</title>
</head>
<body>
    <br><br>
    <div style="text-align: center;">
        <div>
            <label>Make db files</label>
            <form method="POST" action="{{ route('testbl.makedir') }}">
                @csrf
                <input id="directory" type="text"  name="directory">
                <ul>
                    <li>
                        <input id="first" type="text"  name="first">
                    </li>
                    <li>
                        <input id="second" type="text"  name="second">
                    </li>
                    <li>
                        <input id="third" type="text"  name="third">
                    </li>
                    <li>
                        <input id="fourth" type="text"  name="fourth">
                    </li>
                </ul>
                <button type="submit">
                    Ok
                </button>
            </form>
        </div>
        <br><br>
        <div>
        <label>Remove directory</label>
        <form method="POST" action="{{ route('testbl.rmdir') }}">
            @csrf
            <input id="directory" type="text"  name="directory">
            <button type="submit">
                Ok
            </button>
        </form>
    </div>
        <br><br>
        <div>
            <label>Clear directory</label>
            <form method="POST" action="{{ route('testbl.cleardir') }}">
                @csrf
                <input id="directory" type="text"  name="directory">
                <button type="submit">
                    Ok
                </button>
            </form>
        </div>
        <br><br>
        <div>
            <label>Upload backup file</label>
            <form method="POST" action="{{ route('testbl.uplbackup') }} " enctype="multipart/form-data">
                @csrf
                <input id="directory" type="text"  name="directory">
                <input id="file" type="file" name="file">
                <button type="submit">
                    Ok
                </button>
            </form>
        </div>
        <br><br>
        <div>
            <label>Create backup file</label>
            <form method="POST" action="{{ route('testbl.createbackup') }} " enctype="multipart/form-data">
                @csrf
                <input id="directory" type="text"  name="directory" value="test">
                <button type="submit">
                    Ok
                </button>
            </form>
        </div>
        <br><br>
        <div>
            <label>Restore DB</label>
            <form method="POST" action="{{ route('testbl.restore') }} " enctype="multipart/form-data">
                @csrf
                <input id="directory" type="text"  name="directory" value="test">
                <button type="submit">
                    Ok
                </button>
            </form>
        </div>
    </div>

</body>
</html>

