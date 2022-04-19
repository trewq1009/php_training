<?php
namespace app\lib;

class Field
{
    public function userList($dataArr)
    {
        $contentHtml = '';
        $mainHtml = '';
        foreach ($dataArr as $item) {
            
            if($item['status'] == 'ALIVE') {
                if($item['email_status'] == 'INACTIVE') {
                    $item['status'] = '이메일 미인증 회원';
                } else {
                    $item['status'] = '일반회원';
                }
            } else if($item['status'] == 'AWAIT') {
                $item['status'] = '탈퇴 신청 회원';
            }
            
            $contentHtml .= sprintf('
                <tr>
                    <th scope="row">%s</th>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>
                        <button type="submit" name="viewUser" value="%s" class="btn btn-outline-info">Info</button>
                    </td>
                </tr>', $item['no'],
                        $item['name'],
                        $item['id'],
                        $item['email'],
                        $item['status'],
                        $item['registered'],
                        $item['no']);
        }
        $mainHtml = $this->userListContent();

        return str_replace('{{content}}', $contentHtml, $mainHtml);
    }


    public function userListContent()
    {
        return '<table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">NAME</th>
                <th scope="col">ID</th>
                <th scope="col">EMAIL</th>
                <th scope="col">STATUS</th>
                <th scope="col">REG_DT</th>
                <th scope="col">VIEW</th>
            </tr>
            </thead>
            <tbody>
                {{content}}
            </tbody>
        </table>';
    }


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