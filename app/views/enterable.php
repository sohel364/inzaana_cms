<?php
    class ZurbPresenter extends Illuminate\Pagination\Presenter {
        public function getActivePageWrapper($text){
            return '<span class="current">'.$text.'</span>';
        }    
        public function getDisabledTextWrapper($text){
            return '<span class="disabled">'.$text.'</span>';
        }    
        public function getPageLinkWrapper($url, $page, $rel = null){
            return '<span><a href="'.$url.'" class="paginateLink" onclick="javascript:return goToCurPage(this);">'.$page.'</a></span>';
        }    
    }
    $paginatorObj   =   new ZurbPresenter($paginator);
?>
<span class="custPaginate">
    <?php echo with($paginatorObj)->render(); ?> 
</span>