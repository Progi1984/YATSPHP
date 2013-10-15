<html>
  <body>
    <h1>Sample8: Sections - appending PHP assignment</h1>
    <p>This is the same as example 7, except the php code uses appending assignments for a more natural coding style. The end result is the same.</p>
    <p>There are {{count_flavor}} flavors. There are {{count_color}} colors.</p>
    <p>What if there is one color per flavor, but we'd like to be able to hide some of the sections with colors? for example, the third one.</p>
    <p>We can use parentloop for this to identify a unique sub-section for hiding but have its variables loop with the parent section.</p>
    <table>
      <tr>
        <td>
          <u>flavor</u>
        </td>
        <td>
          <u>color</u>
        </td>
      </tr>
      {{section:flavors}}
      <tr>
        <td>{{flavor}}</td>
        <td>{{section:color parentloop="yes"}}<font color="{{color}}">{{color}}</font>{{/section:color}}</td>
      </tr>
      {{/section:flavors}}
    </table>
    <hr>
    <a href="sample07.php">Previous</a>
  </body>
</html>