<?php
namespace app\lib;

class Field
{
    public static function listBtn($data)
    {
        $btnHtml = '<nav aria-label="Page navigation example"><ul class="pagination">';
        if(ceil($data['total'] / $data['resultOnPage']) > 0) {

            if($data['page'] > 1) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] - 1 .'">Previous</a></li>';
            }

            if($data['page'] > 3) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page=1">1</a></li>
                             <li class="page-item" aria-current="page">...</li>';
            }

            if($data['page']-2 > 0) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] - 2 .'">'. $data['page'] - 2 .'</a></li>';
            }

            if($data['page']-1 > 0) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] - 1 .'">'. $data['page'] - 1 .'</a></li>';
            }

            $btnHtml .= '<li class="page-item active" aria-current="page"><span class="page-link">'. $data['page'] .'</span></li>';

            if($data['page'] + 1 < ceil($data['total'] / $data['resultOnPage']) + 1) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] + 1 .'">'. $data['page'] + 1 .'</a></li>';
            }

            if($data['page'] + 2 < ceil($data['total'] / $data['resultOnPage']) + 1) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] + 2 .'">'. $data['page'] + 2 .'</a></li>';
            }

            if($data['page'] < ceil($data['total'] / $data['resultOnPage']) - 2) {
                $btnHtml .= '<li class="page-item" aria-current="page">...</li>
                             <li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. ceil($data['total'] / $data['resultOnPage']) .'">'.ceil($data['total'] / $data['resultOnPage']).'</a></li>';
            }

            if($data['page'] < ceil($data['total'] / $data['resultOnPage'])) {
                $btnHtml .= '<li class="page-item"><a class="page-link" href="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'?page='. $data['page'] + 1 .'">Next</a></li>';
            }

            $btnHtml .= '</ul></nav>';

            return $btnHtml;
        } else {
            return '';
        }
    }

}