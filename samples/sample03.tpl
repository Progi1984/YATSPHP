<html><body>

<h1>Sample3: Sections - Multiple Variables</h1>

<p>
  There are {{count_color}} colors and {{count_flavor}} flavors. The mixed section,
  which references both variables, will loop only until the lesser of the two is reached.
</p>

<h2>colors</h2>
<ol>

  {{section:colors}}
  <li>color: {{color}} </li>
  {{/section:colors}}

</ol>

<h2>flavors</h2>
<ol>

  {{section:flavors}}
  <li>flavor: {{flavor}} </li>
  {{/section:flavors}}

</ol>

<h2>mixed</h2>
<ol>

  {{section:mixed}}
  <li>color: {{color}}, flavor: {{flavor}} </li>
  {{/section:mixed}}

</ol>


<hr>
<a href="sample2.php">Previous</a> | <a href="sample4.php">Next</a>


</body></html>