
<style>
    .carousel-item>img{
        object-fit:fill !important;
    }
    #carouselExampleControls .carousel-inner{
        height:280px !important;
    }
</style>
<?php 
$brands = isset($_GET['b']) ? json_decode(urldecode($_GET['b'])) : array();
?>
<section class="py-0">
    <div class="container">
        <div class="col-lg-12 py-2">
            <div class="row">
                <div class="col-md-12">
                    <div id="carouselExampleControls" class="carousel slide bg-dark" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php 
                                $upload_path = "uploads/banner";
                                if(is_dir(base_app.$upload_path)): 
                                $file= scandir(base_app.$upload_path);
                                $_i = 0;
                                    foreach($file as $img):
                                        if(in_array($img,array('.','..')))
                                            continue;
                                $_i++;
                                    
                            ?>
                            <div class="carousel-item h-100 <?php echo $_i == 1 ? "active" : '' ?>">
                                <img src="<?php echo validate_image($upload_path.'/'.$img) ?>" class="d-block w-100  h-100" alt="<?php echo $img ?>">
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        </div>
                </div>
            </div>
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-4 row-cols-md-3 row-cols-xl-4 ">
                    <?php 
                       $qry = ($conn && $conn instanceof mysqli && !$conn->connect_error) ? @$conn->query("SELECT r.*, t.name as rtype FROM `real_estate_list` r inner join type_list t on r.type_id = t.id where r.status = 1 order by r.`name` asc") : false;
                       if ($qry && $qry instanceof mysqli_result && $qry->num_rows > 0):
                           while($row = $qry->fetch_assoc()):
                            $meta_qry = @$conn->query("SELECT * FROM `real_estate_meta` where real_estate_id = '{$row['id']}' ");
                            $meta = ($meta_qry && $meta_qry instanceof mysqli_result) ? array_column($meta_qry->fetch_all(MYSQLI_ASSOC),"meta_value", "meta_field") : [];
                    ?>
                    <div class="col mb-5">
                        <a class="card product-item text-reset text-decoration-none" href=".?p=view_estate&id=<?php echo ($row['id']) ?>">
                            <!-- Product image-->
                            <div class="overflow-hidden shadow product-holder">
                            <img class="card-img-top w-100 product-cover" src="<?php echo validate_image(isset($meta['thumbnail_path']) ? $meta['thumbnail_path'] : "") ?>" alt="..." />
                            </div>
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <h5 class="card-title w-100 font-weight-bolder"><?= $row['name'] ?></h5>
                                <p class="m-0"><?php echo $row['rtype'] ?></small></p>
                                <p class="m-0"><?= isset($meta['purpose']) ? $meta['purpose'] : "" ?></small></p>
                                <p class="m-0"><?= isset($meta['type']) ? $meta['type'] : "" ?></small></p>
                                <p class="m-0"><?= isset($meta['sale_price']) ? format_num($meta['sale_price']) : "" ?></small></p>
                            </div>
                        </a>
                    </div>
                    <?php 
                           endwhile;
                       else:
                    ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info shadow-sm d-inline-block text-left p-4">
                            <h5><i class="fas fa-info-circle mr-2"></i> Real Estate Portal Ready</h5>
                            <p class="mb-2">Aiven Cloud MySQL setup is available to initialize all tables and listings in 1 click.</p>
                            <a href="<?php echo base_url; ?>db_setup.php" class="btn btn-primary btn-sm"><i class="fas fa-database mr-1"></i> Open Database Setup Tool</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    </div>
</section>
<script>
    function _filter(){
        var brands = []
            $('.brand-item:checked').each(function(){
                brands.push($(this).val())
            })
        _b = JSON.stringify(brands)
        var checked = $('.brand-item:checked').length
        var total = $('.brand-item').length
        if(checked == total)
            location.href="./?";
        else
            location.href="./?b="+encodeURI(_b);
    }
    function check_filter(){
        var checked = $('.brand-item:checked').length
        var total = $('.brand-item').length
        if(checked == total){
            $('#brandAll').attr('checked',true)
        }else{
            $('#brandAll').attr('checked',false)
        }
        if('<?php echo isset($_GET['b']) ?>' == '')
            $('#brandAll,.brand-item').attr('checked',true)
    }
    $(function(){
        check_filter()
        $('#brandAll').change(function(){
            if($(this).is(':checked') == true){
                $('.brand-item').attr('checked',true)
            }else{
                $('.brand-item').attr('checked',false)
            }
            _filter()
        })
        $('.brand-item').change(function(){
            _filter()
        })
    })

</script>