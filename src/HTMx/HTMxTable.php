<?php

namespace rbwebdesigns\core\HTMx;

/**
 * A table that loads data using HTMx.
 */
abstract class HTMxTable
{
    protected string $id;

    public int $perPage = 20;

    public string $class = "";

    public function __construct()
    {
        $this->id = uniqid("table_");
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the names of the columns in the table.
     * These will be added as <th>.
     * 
     * @return array<string>
     */
    abstract protected function getColumns(): array;

    /**
     * Create the link used for each page link in pagination menu.
     */
    abstract protected function getPaginationUrl(int $page): string;

    /**
     * Get total rows for all data in the table.
     */
    abstract protected function getTotalRows(): int;

    /**
     * Get all data for a page.
     */
    abstract protected function getPageData(int $limit, int $page): array;

    /**
     * Takes a row of raw data and formats it.
     * 
     * @return array|string Return an array with column data.
     *   Return a string with the markup for a table row.
     */
    abstract protected function formatRow(array $data): array|string;

    /**
     * Get actions for a table.
     * 
     * @return \rbwebdesigns\core\HTMx\HTMXTableActionInterface[]
     */
    protected function getActions(): array
    {
        return [];
    }

    /**
     * Output the table.
     */
    public function render()
    {
        $header = $links = "";
        $actions = $this->getActions();

        foreach ($this->getColumns() as $column) {
            $header .= "<th>$column</th>";
        }

        if (count($actions) > 0) {
            // Add an extra column.
            $header .= "<th></th>";
        }

        for ($i = 1; $i <= floor($this->getTotalRows() / 20) + 1; $i++) {
            $url = $this->getPaginationUrl($i);
            $links .= "<button hx-get=\"$url\" hx-target=\"#{$this->id} tbody\">$i</button>";
        }

        $initialContent = $this->getPageData($this->perPage, 1);
        $formattedInitialRows = [];
        foreach ($initialContent as $item) {
            $formattedRow = $this->formatRow($item);
            if (is_array($formattedRow)) {
                $finalRowData = implode("", array_map(
                    fn($column) => "<td>$column</td>",
                    $this->formatRow($item)
                ));
                $formattedRow = "<tr>$finalRowData</tr>";
            }

            if (count($actions) > 0) {
                $actionLinks = [];
                foreach ($actions as $action) {
                    $actionLinks[] = $action->render($item);
                }
                $actionsRow = implode(" | ", $actionLinks);
                $formattedRow = str_ireplace("</tr>", "<td>$actionsRow</td></tr>", $formattedRow);
            }

            $formattedInitialRows[] = $formattedRow;
        }
        $formattedInitialRows = implode('', $formattedInitialRows);

        $script = "";
        if (count($actions) > 0) {
            $script = <<<SCRIPT
            <script>
                document.querySelectorAll("a[hx-target='#action-dialog']").forEach((link) => {
                    // Only add listener once.
                    // Could also give something an ID...
                    if (link.dataset.openDialog) {
                        return;
                    }
                    link.dataset.openDialog = "true";

                    link.addEventListener("click", (event) => {
                        document.querySelector("dialog#action-dialog").show();
                    });
                });
            </script>
            SCRIPT;
        }

        return <<<OUTPUT
            <div id="{$this->id}">
                <table class="{$this->class}">
                    <thead>$header</thead>
                    <tbody>$formattedInitialRows</tbody>
                </table>
                $script
                <div class="pagination">
                    $links
                </div>
            </div>
        OUTPUT;
    }
}
