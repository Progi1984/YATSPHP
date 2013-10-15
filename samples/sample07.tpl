<html>
  <body>
    <h1>Sample7: Sections - parentloop</h1>
    <p>
      There are {{count_flavor}} flavors.
      There are {{count_color}} colors.
    </p>
    <p>
      What if there is one color per flavor, but we'd like to be able to hide some of the sections with colors? for example, the third one.
    </p>
    <p>
      We can use parentloop for this to identify a unique sub-section for hiding but have its variables loop with the parent section.
    </p>
    <table>
      <tr>
        <td><u>flavor</u></td>
        <td><u>color</u></td>
      </tr>
      {{section:flavors}}
        <tr>
          <td>{{flavor}}</td>
          <td>{{section:color parentloop="yes"}}<font color="{{color}}">{{color}}</font>{{/section:color}}</td>
        </tr>
      {{/section:flavors}}
    </table>
    <hr>
    <a href="sample06.php">Previous</a> | <a href="sample08.php">Next</a>
  </body>
</html>