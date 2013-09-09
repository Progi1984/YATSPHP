<html><body>

<h1>Sample6: Sections - nesting</h1>

<p>
  There are {{count_flavor}} flavors.
  There are {{count_color}} colors.
</p>

<p>
  Suppose that each flavor comes in each of the colors. We can
  display this using nested sections.
</p>


<table>
  <tr><td><u>flavor</u></td><td><u>color</u></td></tr>
  {{section:flavors}}
  <tr><td>{{flavor}}</td>
    <td>{{section:colors}}<font color="{{color}}">{{color}} </font>{{/section:colors}}</td></tr>
  {{/section:flavors}}
</table>

<hr>
<a href="sample5.php">Previous</a> | <a href="sample7.php">Next</a>


</body></html>