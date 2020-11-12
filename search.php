<html>
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <table
        id="table"
        data-url="json/data1.json"
        data-filter-control="true"
        data-show-search-clear-button="true">
        <thead>
            <tr>
            <th data-field="name" data-filter-control="input">Name</th>
            <th data-field="price" data-filter-control="select">Type</th>
            <th data-field="id">Year</th>
            <th data-field="id">Length</th>
            <th data-field="id">IMBd rating</th>
            <th data-field="id">HW rating</th>
            </tr>
        </thead>
        </table>

        <script>
        $(function() {
            $('#table').bootstrapTable()
        })
        </script>
</html>