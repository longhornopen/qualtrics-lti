<html>
<body>
<form method="POST">
    @csrf
    Launch as:<br>
    <select name="is_teacher">
        <option value="true">Teacher</option>
        <option value="false" disabled="disabled">Student</option>
    </select>

    <br><br>

    resource_link_dbid of assignment to launch:
    <input type="text" name="resource_link_dbid">

    <br><br>

    <button type="submit">Launch assignment</button>
</form>
</body>
</html>
