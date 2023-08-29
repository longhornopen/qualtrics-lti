<!-- Test survey for use during development, if Qualtrics is unavailable or too much trouble -->
<html>
<body>
Click the grade you want.<br>
<a href="{{request()->query()['return_url']}}?Score=0&MaxScore=100">0%</a><br>
<a href="{{request()->query()['return_url']}}?Score=100&MaxScore=100">100%</a><br>
</body>
</html>
