<html>
  <body>
    <h1>Sample2: Sections - Implicit Looping</h1>
    <p>The entire section will loop until all the colors have been displayed.</p>
    <ul>
      {{section:colors}}<li>color: {{color}} </li>{{/section:colors}}
    </ul>
    <hr>
    <a href="sample01.php">Previous</a> | <a href="sample03.php">Next</a>
  </body>
</html>