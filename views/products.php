<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <h2> Product List </h2></div>
                <div class="col-md-7 text-end">
                    <a class="btn btn-sm btn-primary py-1 px-2" href="add-product"> ADD</a>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-secondary py-1 px-2" onclick="massDelete()">MASS DELETE</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <hr class="p-0 m-0"/>
        </div>
        <div class="col-12">
            <div class="row gap-md-4  p-3">
                <?php
                if (isset($products) && $products) {
                    foreach ($products as $item) {
//                        var_dump($item);exit();
                        ?>
                        <div class="col-md-3 col-lg-3 position-relative shadow p-3">
                            <input type="checkbox"
                                   class="delete-checkbox position-absolute top-0 pt-2 form-check-input"
                                   value="<?php echo $item['id']; ?>"/>
                            <div class="container text-center">
                                <h4><?php echo $item['name'] ?? '' ?></h4>
                                <p class="my-1 py-1">
                                    <?php echo $item['category_name'] ?? '' ?> </p>
                                <p class="my-1 py-1"><?php echo ($item['price'] ? number_format((float)$item['price']) : null) . ($item['symbol'] ?? null); ?></p>
                                <p class="my-1 py-1"><?php echo ($item['attrib_key'] ?? '') . ': ' . ($item['attrib_value'] ?? ''); ?> </p>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        let massDelete = () => {
            let checkBoxes = $('.delete-checkbox:checkbox:checked');
            let ids = [];
            checkBoxes.each(function () {
                ids.push($(this).val());
            });
            if (ids && ids.length > 0) {
                $.post('product/mass-delete', {ids},
                    function (data) {
                        location.reload();
                    })
            }

        }
    </script>
</div>