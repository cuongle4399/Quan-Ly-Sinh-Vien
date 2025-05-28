<link rel="stylesheet" href="../css/phantrang.css">
<?php
// phantrang.php

class Pagination {
    private $conn;
    private $records_per_page;
    private $total_records;
    private $current_page;
    private $base_url;
    private $query;
    private $params;
    private $param_types;

    public function __construct($conn, $records_per_page = 10) {
        $this->conn = $conn;
        $this->records_per_page = $records_per_page;
        $this->current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    }

    public function setQuery($query, $params = [], $param_types = "") {
        $this->query = $query;
        $this->params = $params;
        $this->param_types = $param_types;

        // Đếm tổng số bản ghi
        $count_query = preg_replace("/SELECT.*?FROM/i", "SELECT COUNT(*) as total FROM", $query);
        $stmt = $this->conn->prepare($count_query);
        if (!$stmt) {
            die("Lỗi chuẩn bị truy vấn COUNT: " . $this->conn->error);
        }
        if (!empty($this->params) && !empty($this->param_types)) {
            // Đảm bảo số lượng tham số khớp với param_types
            if (strlen($this->param_types) === count($this->params)) {
                $stmt->bind_param($this->param_types, ...$this->params);
            } else {
                die("Lỗi: Số lượng tham số không khớp với kiểu dữ liệu trong truy vấn COUNT.");
            }
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->total_records = $row ? ($row['total'] ?? 0) : 0;
    }

    public function getData() {
        $offset = ($this->current_page - 1) * $this->records_per_page;
        $query = $this->query . " LIMIT ? OFFSET ?";
        $params = $this->params;
        $param_types = $this->param_types . "ii";
        $params[] = $this->records_per_page;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Lỗi chuẩn bị truy vấn dữ liệu: " . $this->conn->error);
        }
        if (!empty($params) && !empty($param_types)) {
            $stmt->bind_param($param_types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function generatePagination($base_url) {
        $this->base_url = $base_url;
        $total_pages = ceil($this->total_records / $this->records_per_page);

        $output = '<div class="pagination">';
        
        // Nút trang trước
        if ($this->current_page > 1) {
            $output .= '<a href="' . $this->buildUrl($this->current_page - 1) . '">« Trước</a>';
        }

        // Các số trang
        $range = 2;
        $start = max(1, $this->current_page - $range);
        $end = min($total_pages, $this->current_page + $range);

        if ($start > 1) {
            $output .= '<a href="' . $this->buildUrl(1) . '">1</a>';
            if ($start > 2) {
                $output .= '<span>...</span>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $this->current_page ? 'class="active"' : '';
            $output .= '<a ' . $active . ' href="' . $this->buildUrl($i) . '">' . $i . '</a>';
        }

        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $output .= '<span>...</span>';
            }
            $output .= '<a href="' . $this->buildUrl($total_pages) . '">' . $total_pages . '</a>';
        }

        // Nút trang sau
        if ($this->current_page < $total_pages) {
            $output .= '<a href="' . $this->buildUrl($this->current_page + 1) . '">Sau »</a>';
        }

        $output .= '</div>';

        return $output;
    }

    private function buildUrl($page) {
        $url = $this->base_url;
        $params = $_GET;
        $params['page'] = $page;
        
        $query_string = http_build_query($params);
        return $url . '?' . $query_string;
    }

    public function getTotalRecords() {
        return $this->total_records;
    }

    public function getCurrentPage() {
        return $this->current_page;
    }
}
?>