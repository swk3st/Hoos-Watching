<html>
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <div class="container"> 
    <table
        class = "table"
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
            <th data-field="id">IMDb rating</th>
            <!-- <th data-field="id">HW rating</th> -->
            </tr>
        </thead>

        <tbody>
            <?php
            $titles = get_titles(0, 25, SORT_TITLES_NUM_STARS, FILTER_TITLES_NONE, null, false);
            foreach ($titles as $title) :
            ?>
                <tr>
                    <th scope="row">
                        <a href="./title.php?tconst=<?php echo $title['tconst']; ?>">
                            <?php echo $title['primaryTitle']; ?>
                        </a>
                        <small class="text-muted"><?php echo $title['titleType']; ?></small>
                    </th>
                    <td>
                        <?php
                        echo $title['startYear'];
                        if (!is_null($title['endYear'])) {
                            echo "-" . $title['endYear'];
                        }
                        ?>
                    </td>
                    <td><?php echo minutes_to_human_time($title['runtimeMinutes']); ?></td>
                    <td><?php
                        echo number_format($title['averageRating'], 1) .
                            " (" .
                            number_format($title['numVotes']) .
                            " votes)";
                        ?></td>
                    <td>
                        <?php
                        if ($title['numUserVotes'] > 0) {
                            echo number_format($title['userRating'], 1) .
                                " (" .
                                number_format($title['numUserVotes']) .
                                " votes)";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        </table>
        </div>
        <!-- <script>
        $(function() {
            $('#table').bootstrapTable()
        })
        </script> -->

</html>