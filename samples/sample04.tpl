<html>
  <body>
    <h1>Sample4: Sections - Multiple Variables with Scalars</h1>
    <p>
      There are {{count_flavor}} flavors. There is also
      a single person, {{person}}.
    </p>
    <h2>flavors with repeatscalar="no"</h2>
    <ol>
      {{section:flavors}}
      <li>{{person}} likes {{flavor}} </li>
      {{/section:flavors}}
    </ol>
    <h2>flavors with repeatscalar="yes"</h2>
    <ol>
      {{section:repeat}}
      <li>{{person repeatscalar="yes"}} likes {{flavor}} </li>
      {{/section:repeat}}
    </ol>
    <h2>flavors with repeatscalar="no", section with autohide="yes"</h2>
    <ol>
      {{section:repeat autohide="yes"}}
      <li>{{person repeatscalar="no"}} likes {{flavor}} </li>
      {{/section:repeat}}
    </ol>
    (It doesn't display anything because autohide sees that some variables are missing for some rows)
    <hr>
    <a href="sample03.php">Previous</a> | <a href="sample05.php">Next</a>
  </body>
</html>