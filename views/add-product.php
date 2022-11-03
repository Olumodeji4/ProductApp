<div class="container-fluid mb-5">
    <form id="product_form" action="store-product" method="post">
        <div class="row mt-5">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-3">
                        <h2> Product Add </h2>
                    </div>
                    <div class="col-md-7 text-end">
                        <button class="btn btn-sm btn-primary py-1 px-2" type="submit"> Save</button>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-sm btn-secondary py-1 px-2" href="products">Cancel</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <hr class="p-0 m-0"/>
            </div>
            <div class="container-fluid mt-5">
                <div class="row" id="productForm">
                    <div class="col-md-8 offset-md-2">
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input required type="text" class="form-control" name="sku" id="sku" placeholder="Sku"
                                   maxlength="20">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input required type="text" class="form-control" name="name" id="name" placeholder="Name"
                                   maxlength="25">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input required type="number" class="form-control" name="price" id="price" placeholder="0.0"
                                   maxlength="15">
                        </div>
                        <div class="mb-3">
                            <select id="productType" onchange="switchSelect(this.value)" class="form-select w-25"
                                    name="relationType"
                                    required>
                                <option value="">Type Switcher</option>
                                <option value="DVD">DVD</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Book">Book</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div id="DVD" class="container d-none productTypes">
                                <label class="form-label">Size (MB)</label>
                                <input required type="text" class="form-control" name="productType[size]" id="size"
                                       placeholder="size" maxlength="8"/>
                                <p>Please provide product size (in MB)</p>
                            </div>
                            <div id="Furniture" class="container d-none productTypes">
                                <label class="form-label">Height (CM)</label>
                                <input required type="text" class="form-control" name="productType[height]" id="height"
                                       placeholder="0" maxlength="4"/>
                                <label class="form-label">Width (CM)</label>
                                <input required type="text" class="form-control" name="productType[width]" id="width"
                                       placeholder="0" maxlength="4"/>
                                <label class="form-label">Length (CM)</label>
                                <input required type="text" class="form-control" name="productType[length]" id="length"
                                       placeholder="0" maxlength="4"/>
                                <p>Please provide product dimensions in HxWxL format</p>
                            </div>
                            <div id="Book" class="container d-none productTypes">
                                <label class="form-label">Weight (KG)</label>
                                <input required type="text" class="form-control" name="productType[weight]" id="weight"
                                       placeholder="0.0" maxlength="8"/>
                                <p>Please provide product weight (in Kg)</p>
                            </div>
                            <div id="empty" class="container productTypes">
                                Select A product type to continue
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        let switchSelect = id => {
            $('.productTypes').hide().addClass('d-none');
            let inputs = $('.productTypes').children('.form-control').removeAttr('required');

            if (id) {
                $('#' + id).show().removeClass('d-none');
                $('#' + id).children('.form-control').attr('required', 'required');
            } else {
                $('#empty').show().removeClass('d-none');
            }

        }
    </script>
</div>