-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 19, 2021 at 12:29 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafemanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_id` varchar(10) NOT NULL,
  `category_name` varchar(20) NOT NULL,
  `category_description` varchar(40) NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `category`:
--

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_id`, `category_name`, `category_description`, `created_by`, `created_date`) VALUES
(20, 'CT0001', 'Milkshake', '', 'Admin', '2021-05-29'),
(21, 'CT0021', 'Pizza', '', 'Admin', '2021-05-29');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `customer_name` varchar(20) NOT NULL,
  `mobile` int(10) DEFAULT NULL,
  `customer_gstin` varchar(20) DEFAULT NULL,
  `address` varchar(40) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pincode` int(10) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `opening_balance` int(11) DEFAULT NULL,
  `sales_due` float DEFAULT NULL,
  `sales_return_due` float DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `created_by` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `customer`:
--

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `customer_id`, `customer_name`, `mobile`, `customer_gstin`, `address`, `city`, `state`, `pincode`, `country`, `opening_balance`, `sales_due`, `sales_return_due`, `created_date`, `created_by`) VALUES
(1, 'CU0001', 'Walk-in Customer', NULL, '', '', '', '', NULL, '', NULL, 0, 0, '2021-05-23 18:30:00', 'Admin'),

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL,
  `email` varchar(64) NOT NULL COMMENT 'It doesnt reference email in table users, this will prevent even unregistered users as well',
  `last_failed_login` int(11) DEFAULT NULL COMMENT 'unix timestamp of last failed login',
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONSHIPS FOR TABLE `failed_logins`:
--

--
-- Dumping data for table `failed_logins`
--

INSERT INTO `failed_logins` (`id`, `email`, `last_failed_login`, `failed_login_attempts`) VALUES
(1, 'yuvaraj412k@gmail.com', NULL, 0),
(3, 'admin', 1621760642, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `item_name` varchar(20) NOT NULL,
  `category_id` varchar(10) NOT NULL,
  `unit_id` varchar(10) NOT NULL,
  `stock_qty` int(10) DEFAULT NULL,
  `minimum_qty` float DEFAULT NULL,
  `purchase_price` float NOT NULL,
  `expire_date` date DEFAULT NULL,
  `profit_margin` float DEFAULT NULL,
  `sales_price` float NOT NULL,
  `description` varchar(40) DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `created_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `items`:
--   `category_id`
--       `category` -> `category_id`
--   `unit_id`
--       `units` -> `unit_id`
--

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_id`, `item_name`, `category_id`, `unit_id`, `stock_qty`, `minimum_qty`, `purchase_price`, `expire_date`, `profit_margin`, `sales_price`, `description`, `created_by`, `created_date`) VALUES
(13, 'IT0001', 'Milkshake1', 'CT0001', 'UT0003', 0, 3, 180, '2021-06-15', 3, 185, '', 'Admin', '2021-06-10'),
(14, 'IT0014', 'Straberry', 'CT0001', 'UT0003', 9, 3, 120, NULL, 2, 122, '', 'Admin', '2021-06-11');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `role_id` int(11) NOT NULL,
  `permission` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `permissions`:
--   `role_id`
--       `roles` -> `id`
--

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`role_id`, `permission`) VALUES
(1, 'users_add'),
(1, 'users_edit'),
(1, 'users_delete'),
(1, 'users_view'),
(1, 'tax_add'),
(1, 'tax_edit'),
(1, 'tax_delete'),
(1, 'tax_view'),
(1, 'units_add'),
(1, 'units_edit'),
(1, 'units_delete'),
(1, 'units_view'),
(1, 'roles_add'),
(1, 'roles_edit'),
(1, 'roles_delete'),
(1, 'roles_view'),
(1, 'expense_add'),
(1, 'expense_edit'),
(1, 'expense_delete'),
(1, 'expense_view'),
(1, 'items_add'),
(1, 'items_edit'),
(1, 'items_delete'),
(1, 'items_view'),
(1, 'suppliers_add'),
(1, 'suppliers_edit'),
(1, 'suppliers_delete'),
(1, 'suppliers_view'),
(1, 'customers_add'),
(1, 'customers_edit'),
(1, 'customers_delete'),
(1, 'customers_view'),
(1, 'purchase_add'),
(1, 'purchase_edit'),
(1, 'purchase_delete'),
(1, 'purchase_view'),
(1, 'sales_add'),
(1, 'sales_edit'),
(1, 'sales_delete'),
(1, 'sales_view'),
(1, 'sales_payment_view'),
(1, 'sales_payment_add'),
(1, 'sales_payment_delete'),
(1, 'sales_report'),
(1, 'purchase_report'),
(1, 'profit_report'),
(1, 'stock_report'),
(1, 'item_sales_report'),
(1, 'purchase_payments_report'),
(1, 'sales_payments_report'),
(1, 'expired_items_report'),
(1, 'items_category_add'),
(1, 'items_category_edit'),
(1, 'items_category_delete'),
(1, 'items_category_view'),
(1, 'expense_category_add'),
(1, 'expense_category_edit'),
(1, 'expense_category_delete'),
(1, 'expense_category_view'),
(1, 'dashboard_view'),
(1, 'purchase_return_add'),
(1, 'purchase_return_edit'),
(1, 'purchase_return_delete'),
(1, 'purchase_return_view'),
(1, 'purchase_return_report'),
(1, 'sales_return_add'),
(1, 'sales_return_edit'),
(1, 'sales_return_delete'),
(1, 'sales_return_view'),
(1, 'sales_return_report'),
(1, 'sales_return_payment_view'),
(1, 'sales_return_payment_add'),
(1, 'sales_return_payment_delete'),
(1, 'purchase_return_payment_view'),
(1, 'purchase_return_payment_add'),
(1, 'purchase_return_payment_delete'),
(1, 'purchase_payment_view'),
(1, 'purchase_payment_add'),
(1, 'purchase_payment_delete'),
(1, 'payment_types_add'),
(1, 'payment_types_edit'),
(1, 'payment_types_delete'),
(1, 'payment_types_view'),


-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `purchase_id` varchar(10) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_time` time DEFAULT NULL,
  `sub_total` float NOT NULL,
  `round_off` float DEFAULT NULL,
  `grand_total` float NOT NULL,
  `supplier_id` varchar(10) NOT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `tax_id` varchar(10) NOT NULL,
  `tax_amt_cgst` float DEFAULT NULL,
  `tax_amt_sgst` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `purchase_status` varchar(10) NOT NULL,
  `reference_no` varchar(20) DEFAULT NULL,
  `discount_on_all_type` varchar(20) DEFAULT NULL,
  `discount_on_all_amt` float DEFAULT NULL,
  `discount_on_all_input` float DEFAULT NULL,
  `other_charges_input` float DEFAULT NULL,
  `other_charges_amt` float DEFAULT NULL,
  `other_charges_type` varchar(20) DEFAULT NULL,
  `return_bit` int(1) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchase`:
--   `supplier_id`
--       `supplier` -> `supplier_id`
--   `tax_id`
--       `tax` -> `tax_id`
--

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`id`, `purchase_id`, `purchase_date`, `purchase_time`, `sub_total`, `round_off`, `grand_total`, `supplier_id`, `created_by`, `tax_id`, `tax_amt_cgst`, `tax_amt_sgst`, `unit_total_cost`, `paid_amount`, `purchase_status`, `reference_no`, `discount_on_all_type`, `discount_on_all_amt`, `discount_on_all_input`, `other_charges_input`, `other_charges_amt`, `other_charges_type`, `return_bit`, `payment_status`) VALUES
(4, 'PR0001', '2021-06-11', '09:06:15', 7200, 0, 8064, 'SP0004', 'Admin', 'TAX0002', NULL, NULL, NULL, 0, 'received', '', 'percentage', 0, NULL, NULL, 0, 'percentage', NULL, 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `purchaseitems`
--

CREATE TABLE `purchaseitems` (
  `id` int(11) NOT NULL,
  `purchase_id` varchar(10) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `purchase_qty` int(10) NOT NULL,
  `price_per_unit` float DEFAULT NULL,
  `discount_input` float DEFAULT NULL,
  `discount_type` varchar(10) DEFAULT NULL,
  `discount_amt` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `total_cost` float DEFAULT NULL,
  `purchase_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchaseitems`:
--   `item_id`
--       `items` -> `item_id`
--   `purchase_id`
--       `purchase` -> `purchase_id`
--

--
-- Dumping data for table `purchaseitems`
--

INSERT INTO `purchaseitems` (`id`, `purchase_id`, `item_id`, `purchase_qty`, `price_per_unit`, `discount_input`, `discount_type`, `discount_amt`, `unit_total_cost`, `total_cost`, `purchase_status`) VALUES
(3, 'PR0001', 'IT0001', 40, 180, 0, 'percentage', 0, 180, 7200, 'ordered');

-- --------------------------------------------------------

--
-- Table structure for table `purchasepayments`
--

CREATE TABLE `purchasepayments` (
  `id` int(11) NOT NULL,
  `purchase_id` varchar(10) NOT NULL,
  `payment_date` date NOT NULL,
  `payment` float NOT NULL,
  `created_date` date NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `created_time` time NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `payment_note` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchasepayments`:
--   `purchase_id`
--       `purchase` -> `purchase_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `purchasereturn`
--

CREATE TABLE `purchasereturn` (
  `id` int(11) NOT NULL,
  `purchase_id` varchar(10) DEFAULT NULL,
  `return_date` date NOT NULL,
  `return_id` varchar(10) DEFAULT NULL,
  `sub_total` float NOT NULL,
  `round_off` float DEFAULT NULL,
  `grand_total` float NOT NULL,
  `supplier_id` varchar(10) NOT NULL,
  `created_time` time DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `tax_id` varchar(10) NOT NULL,
  `tax_amt_cgst` float DEFAULT NULL,
  `tax_amt_sgst` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `return_status` varchar(10) NOT NULL,
  `reference_no` varchar(20) DEFAULT NULL,
  `discount_on_all_type` varchar(20) DEFAULT NULL,
  `discount_on_all_amt` float DEFAULT NULL,
  `discount_on_all_input` float DEFAULT NULL,
  `other_charges_input` float DEFAULT NULL,
  `other_charges_amt` float DEFAULT NULL,
  `other_charges_type` varchar(20) DEFAULT NULL,
  `return_bit` int(1) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchasereturn`:
--   `purchase_id`
--       `purchase` -> `purchase_id`
--   `supplier_id`
--       `supplier` -> `supplier_id`
--   `tax_id`
--       `tax` -> `tax_id`
--

--
-- Dumping data for table `purchasereturn`
--

INSERT INTO `purchasereturn` (`id`, `purchase_id`, `return_date`, `return_id`, `sub_total`, `round_off`, `grand_total`, `supplier_id`, `created_time`, `created_by`, `tax_id`, `tax_amt_cgst`, `tax_amt_sgst`, `unit_total_cost`, `paid_amount`, `return_status`, `reference_no`, `discount_on_all_type`, `discount_on_all_amt`, `discount_on_all_input`, `other_charges_input`, `other_charges_amt`, `other_charges_type`, `return_bit`, `payment_status`) VALUES
(3, NULL, '2021-06-10', 'PR0001', 1080, 0, 1209.6, 'SP0004', '11:06:36', 'Admin', 'TAX0002', NULL, NULL, NULL, 1400, 'return', '', 'percentage', 0, 0, 0, 0, 'percentage', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `purchasereturnitems`
--

CREATE TABLE `purchasereturnitems` (
  `id` int(11) NOT NULL,
  `return_id` varchar(10) NOT NULL,
  `purchase_id` varchar(10) DEFAULT NULL,
  `item_id` varchar(10) NOT NULL,
  `return_qty` int(10) NOT NULL,
  `price_per_unit` float DEFAULT NULL,
  `discount_input` float DEFAULT NULL,
  `discount_type` varchar(10) DEFAULT NULL,
  `discount_amt` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `total_cost` float DEFAULT NULL,
  `return_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchasereturnitems`:
--   `item_id`
--       `items` -> `item_id`
--   `purchase_id`
--       `purchase` -> `purchase_id`
--   `return_id`
--       `purchasereturn` -> `return_id`
--

--
-- Dumping data for table `purchasereturnitems`
--

INSERT INTO `purchasereturnitems` (`id`, `return_id`, `purchase_id`, `item_id`, `return_qty`, `price_per_unit`, `discount_input`, `discount_type`, `discount_amt`, `unit_total_cost`, `total_cost`, `return_status`) VALUES
(5, 'PR0001', NULL, 'IT0001', 6, 180, 0, 'percentage', 0, 180, 1080, 'return');

-- --------------------------------------------------------

--
-- Table structure for table `purchasereturnpayments`
--

CREATE TABLE `purchasereturnpayments` (
  `id` int(11) NOT NULL,
  `return_id` varchar(10) NOT NULL,
  `purchase_id` varchar(10) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment` float NOT NULL,
  `created_date` date NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `created_time` time NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `payment_note` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `purchasereturnpayments`:
--   `purchase_id`
--       `purchase` -> `purchase_id`
--   `return_id`
--       `purchasereturn` -> `return_id`
--

--
-- Dumping data for table `purchasereturnpayments`
--

INSERT INTO `purchasereturnpayments` (`id`, `return_id`, `purchase_id`, `payment_date`, `payment`, `created_date`, `payment_type`, `created_time`, `created_by`, `payment_note`) VALUES
(2, 'PR0001', NULL, '2021-06-10', 200, '2021-10-10', 'cash', '11:06:36', 'Admin', ''),
(3, 'PR0001', NULL, '2021-06-10', 200, '2021-10-10', 'cash', '11:06:12', 'Admin', ''),
(4, 'PR0001', NULL, '2021-06-10', 1000, '2021-10-10', 'cash', '11:06:18', 'Admin', '');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL,
  `role_description` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `roles`:
--

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_description`) VALUES
(1, 'Default', ''),
(4, 'user', '');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sales_id` varchar(10) NOT NULL,
  `sales_date` date NOT NULL,
  `sales_time` time DEFAULT NULL,
  `sub_total` float NOT NULL,
  `round_off` float DEFAULT NULL,
  `grand_total` float NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `tax_id` varchar(10) NOT NULL,
  `tax_amt_cgst` float DEFAULT NULL,
  `tax_amt_sgst` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `sales_status` varchar(10) NOT NULL,
  `reference_no` varchar(20) DEFAULT NULL,
  `discount_on_all_type` varchar(20) DEFAULT NULL,
  `discount_on_all_amt` float DEFAULT NULL,
  `discount_on_all_input` float DEFAULT NULL,
  `other_charges_input` float DEFAULT NULL,
  `other_charges_amt` float DEFAULT NULL,
  `other_charges_type` varchar(20) DEFAULT NULL,
  `return_bit` int(1) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `sales`:
--   `customer_id`
--       `customer` -> `customer_id`
--   `tax_id`
--       `tax` -> `tax_id`
--

--
-- Dumping data for table `sales`
--


-- --------------------------------------------------------

--
-- Table structure for table `salesitems`
--

CREATE TABLE `salesitems` (
  `id` int(11) NOT NULL,
  `sales_id` varchar(10) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `sales_qty` int(10) NOT NULL,
  `price_per_unit` float DEFAULT NULL,
  `discount_input` float DEFAULT NULL,
  `discount_type` varchar(10) DEFAULT NULL,
  `discount_amt` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `total_cost` float DEFAULT NULL,
  `sales_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `salesitems`:
--   `item_id`
--       `items` -> `item_id`
--   `sales_id`
--       `sales` -> `sales_id`
--

--
-- Dumping data for table `salesitems`
--

INSERT INTO `salesitems` (`id`, `sales_id`, `item_id`, `sales_qty`, `price_per_unit`, `discount_input`, `discount_type`, `discount_amt`, `unit_total_cost`, `total_cost`, `sales_status`) VALUES
(14, 'SL0001', 'IT0001', 1, 185, 0, 'percentage', 0, 185, 185, 'final'),
(15, 'SL0009', 'IT0001', 1, 185, 0, 'percentage', 0, 185, 185, 'final'),
(16, 'SL0010', 'IT0001', 1, 185, 0, 'percentage', 0, 185, 185, 'final'),
(17, 'SL0011', 'IT0014', 1, 122, 0, 'percentage', 0, 122, 122, 'final'),
(18, 'SL0013', 'IT0001', 1, 185, 0, 'percentage', 0, 185, 185, 'final');

-- --------------------------------------------------------

--
-- Table structure for table `salespayments`
--

CREATE TABLE `salespayments` (
  `id` int(11) NOT NULL,
  `sales_id` varchar(10) NOT NULL,
  `payment_date` date NOT NULL,
  `payment` float NOT NULL,
  `created_date` date NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `created_time` time NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `payment_note` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `salespayments`:
--   `sales_id`
--       `sales` -> `sales_id`
--

--
-- Dumping data for table `salespayments`
--

INSERT INTO `salespayments` (`id`, `sales_id`, `payment_date`, `payment`, `created_date`, `payment_type`, `created_time`, `created_by`, `payment_note`) VALUES
(10, 'SL0001', '2021-06-10', 207, '2021-10-10', 'cash', '09:06:09', 'Admin', '');

-- --------------------------------------------------------

--
-- Table structure for table `salesreturn`
--

CREATE TABLE `salesreturn` (
  `id` int(11) NOT NULL,
  `sales_id` varchar(10) DEFAULT NULL,
  `return_date` date NOT NULL,
  `return_id` varchar(10) DEFAULT NULL,
  `sub_total` float NOT NULL,
  `round_off` float DEFAULT NULL,
  `grand_total` float NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `created_time` time DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `tax_id` varchar(10) NOT NULL,
  `tax_amt_cgst` float DEFAULT NULL,
  `tax_amt_sgst` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `return_status` varchar(10) NOT NULL,
  `reference_no` varchar(20) DEFAULT NULL,
  `discount_on_all_type` varchar(20) DEFAULT NULL,
  `discount_on_all_amt` float DEFAULT NULL,
  `discount_on_all_input` float DEFAULT NULL,
  `other_charges_input` float DEFAULT NULL,
  `other_charges_amt` float DEFAULT NULL,
  `other_charges_type` varchar(20) DEFAULT NULL,
  `return_bit` int(1) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `salesreturn`:
--   `customer_id`
--       `customer` -> `customer_id`
--   `sales_id`
--       `sales` -> `sales_id`
--   `tax_id`
--       `tax` -> `tax_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `salesreturnitems`
--

CREATE TABLE `salesreturnitems` (
  `id` int(11) NOT NULL,
  `return_id` varchar(10) NOT NULL,
  `sales_id` varchar(10) DEFAULT NULL,
  `item_id` varchar(10) NOT NULL,
  `return_qty` int(10) NOT NULL,
  `price_per_unit` float DEFAULT NULL,
  `discount_input` float DEFAULT NULL,
  `discount_type` varchar(10) DEFAULT NULL,
  `discount_amt` float DEFAULT NULL,
  `unit_total_cost` float DEFAULT NULL,
  `total_cost` float DEFAULT NULL,
  `return_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `salesreturnitems`:
--   `item_id`
--       `items` -> `item_id`
--   `return_id`
--       `salesreturn` -> `return_id`
--   `sales_id`
--       `sales` -> `sales_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `salesreturnpayments`
--

CREATE TABLE `salesreturnpayments` (
  `id` int(11) NOT NULL,
  `return_id` varchar(10) NOT NULL,
  `sales_id` varchar(10) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment` float NOT NULL,
  `created_date` date NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `created_time` time NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `payment_note` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `salesreturnpayments`:
--   `return_id`
--       `salesreturn` -> `return_id`
--   `sales_id`
--       `salesreturn` -> `sales_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `shopdetails`
--

CREATE TABLE `shopdetails` (
  `id` int(11) NOT NULL,
  `shop_name` varchar(40) NOT NULL,
  `shop_mobile` int(10) NOT NULL,
  `shop_phone` varchar(20) NOT NULL,
  `shop_email` varchar(40) NOT NULL,
  `shop_address` varchar(40) NOT NULL,
  `shop_city` varchar(20) NOT NULL,
  `shop_state` varchar(20) NOT NULL,
  `shop_pincode` int(10) NOT NULL,
  `shop_gstin` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `shopdetails`:
--

--
-- Dumping data for table `shopdetails`
--

INSERT INTO `shopdetails` (`id`, `shop_name`, `shop_mobile`, `shop_phone`, `shop_email`, `shop_address`, `shop_city`, `shop_state`, `shop_pincode`, `shop_gstin`) VALUES
(1, 'Cafe', 987654321, '0413 12345', 'cafe@example.com', 'no:1, cafe street', 'example city', 'puducherry', 605004, 'gst000000001');

-- --------------------------------------------------------

--
-- Table structure for table `stockentry`
--

CREATE TABLE `stockentry` (
  `id` int(11) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `entry_date` date DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL,
  `note` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `stockentry`:
--   `item_id`
--       `items` -> `item_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `supplier_id` varchar(10) NOT NULL,
  `supplier_name` varchar(20) NOT NULL,
  `mobile` int(10) DEFAULT NULL,
  `supplier_gstin` varchar(20) DEFAULT NULL,
  `address` varchar(40) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pincode` int(10) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `opening_balance` float DEFAULT NULL,
  `purchase_due` float DEFAULT NULL,
  `purchase_return_due` float DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `created_by` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `supplier`:
--

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `supplier_id`, `supplier_name`, `mobile`, `supplier_gstin`, `address`, `city`, `state`, `pincode`, `country`, `opening_balance`, `purchase_due`, `purchase_return_due`, `created_date`, `created_by`) VALUES
(4, 'SP0004', 'Supplier 3', NULL, '', '', '', '', NULL, '', NULL, 0, -190.4, '2021-06-09 18:30:00', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `id` int(11) NOT NULL,
  `tax_id` varchar(10) NOT NULL,
  `tax_name` varchar(20) NOT NULL,
  `tax` float NOT NULL,
  `tax_description` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `tax`:
--

--
-- Dumping data for table `tax`
--

INSERT INTO `tax` (`id`, `tax_id`, `tax_name`, `tax`, `tax_description`) VALUES
(1, 'TAX0001', 'CGST 18%', 18, ''),
(3, 'TAX0002', 'GST 12%', 12, '');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_id` varchar(10) NOT NULL,
  `unit_name` varchar(20) NOT NULL,
  `unit_description` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `units`:
--

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_id`, `unit_name`, `unit_description`) VALUES
(2, 'UT0001', 'Box', ''),
(3, 'UT0003', 'Pieces', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `session_id` varchar(48) DEFAULT NULL,
  `cookie_token` varchar(128) DEFAULT NULL,
  `name` varchar(48) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(64) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(48) NOT NULL DEFAULT 'default.png' COMMENT 'The base name for the image. Its not always unique because of default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONSHIPS FOR TABLE `users`:
--   `role_id`
--       `roles` -> `id`
--

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `session_id`, `cookie_token`, `name`, `password`, `email`, `role_id`, `profile_picture`) VALUES
(1, '20ranc6vemoqht5uklvf4l1f9o', NULL, 'Yuva', '$2y$10$L/AkD09JAjb7.OpB1ORZy.lO3Kz7wN5RezuJ9GzXQW8j6VFC4U4Qa', 'admin', 1, 'default.png'),
(2, '3jfd5pvkvns24eoot74m01rfih', NULL, 'Sales', '$2y$10$L/AkD09JAjb7.OpB1ORZy.lO3Kz7wN5RezuJ9GzXQW8j6VFC4U4Qa', 'sales', 4, 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email` (`email`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`),
  ADD KEY `fk_unit_id` (`unit_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD KEY `fk_role_id_permissions` (`role_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_supplier_id_purchase` (`supplier_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `fk_tax_id_purchase` (`tax_id`);

--
-- Indexes for table `purchaseitems`
--
ALTER TABLE `purchaseitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_id_purchaseitems` (`item_id`),
  ADD KEY `fk_purchase_id_purchaseitems` (`purchase_id`);

--
-- Indexes for table `purchasepayments`
--
ALTER TABLE `purchasepayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_id_purchasepayments` (`purchase_id`);

--
-- Indexes for table `purchasereturn`
--
ALTER TABLE `purchasereturn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_id_purchasereturn` (`purchase_id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `fk_supplier_id_purchasereturn` (`supplier_id`),
  ADD KEY `fk_tax_id_purchasereturn` (`tax_id`);

--
-- Indexes for table `purchasereturnitems`
--
ALTER TABLE `purchasereturnitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_id_purchasereturnitems` (`purchase_id`),
  ADD KEY `fk_item_id_purchasereturnitems` (`item_id`),
  ADD KEY `fk_return_id_purchasereturnitems` (`return_id`);

--
-- Indexes for table `purchasereturnpayments`
--
ALTER TABLE `purchasereturnpayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_return_id_purchasereturnpayments` (`return_id`),
  ADD KEY `fk_purchase_id_purchasereturnpayments` (`purchase_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_id` (`customer_id`),
  ADD KEY `fk_tax_id` (`tax_id`),
  ADD KEY `sales_id` (`sales_id`);

--
-- Indexes for table `salesitems`
--
ALTER TABLE `salesitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_id_salesitem` (`item_id`),
  ADD KEY `fk_sales_id_salesitem` (`sales_id`);

--
-- Indexes for table `salespayments`
--
ALTER TABLE `salespayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salesid_salespayment` (`sales_id`);

--
-- Indexes for table `salesreturn`
--
ALTER TABLE `salesreturn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `fk_salesid_salesreturn` (`sales_id`),
  ADD KEY `fk_tax_id_salesreturn` (`tax_id`),
  ADD KEY `fk_customer_id_salesreturn` (`customer_id`);

--
-- Indexes for table `salesreturnitems`
--
ALTER TABLE `salesreturnitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_id_salesreturnitems` (`sales_id`),
  ADD KEY `fk_return_id_salesreturnitems` (`return_id`),
  ADD KEY `fk_item_id_salesreturnitems` (`item_id`);

--
-- Indexes for table `salesreturnpayments`
--
ALTER TABLE `salesreturnpayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salesid_salespayment` (`sales_id`),
  ADD KEY `fk_return_id_salesreturnpayments` (`return_id`);

--
-- Indexes for table `shopdetails`
--
ALTER TABLE `shopdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockentry`
--
ALTER TABLE `stockentry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_id` (`item_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_id` (`tax_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role_id_users` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchaseitems`
--
ALTER TABLE `purchaseitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchasepayments`
--
ALTER TABLE `purchasepayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchasereturn`
--
ALTER TABLE `purchasereturn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchasereturnitems`
--
ALTER TABLE `purchasereturnitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchasereturnpayments`
--
ALTER TABLE `purchasereturnpayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `salesitems`
--
ALTER TABLE `salesitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `salespayments`
--
ALTER TABLE `salespayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `salesreturn`
--
ALTER TABLE `salesreturn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `salesreturnitems`
--
ALTER TABLE `salesreturnitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `salesreturnpayments`
--
ALTER TABLE `salesreturnpayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shopdetails`
--
ALTER TABLE `shopdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stockentry`
--
ALTER TABLE `stockentry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `fk_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_role_id_permissions` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `fk_supplier_id_purchase` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `fk_tax_id_purchase` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`);

--
-- Constraints for table `purchaseitems`
--
ALTER TABLE `purchaseitems`
  ADD CONSTRAINT `fk_item_id_purchaseitems` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `fk_purchase_id_purchaseitems` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`);

--
-- Constraints for table `purchasepayments`
--
ALTER TABLE `purchasepayments`
  ADD CONSTRAINT `fk_purchase_id_purchasepayments` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`);

--
-- Constraints for table `purchasereturn`
--
ALTER TABLE `purchasereturn`
  ADD CONSTRAINT `fk_purchase_id_purchasereturn` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`),
  ADD CONSTRAINT `fk_supplier_id_purchasereturn` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `fk_tax_id_purchasereturn` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`);

--
-- Constraints for table `purchasereturnitems`
--
ALTER TABLE `purchasereturnitems`
  ADD CONSTRAINT `fk_item_id_purchasereturnitems` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `fk_purchase_id_purchasereturnitems` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`),
  ADD CONSTRAINT `fk_return_id_purchasereturnitems` FOREIGN KEY (`return_id`) REFERENCES `purchasereturn` (`return_id`);

--
-- Constraints for table `purchasereturnpayments`
--
ALTER TABLE `purchasereturnpayments`
  ADD CONSTRAINT `fk_purchase_id_purchasereturnpayments` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`),
  ADD CONSTRAINT `fk_return_id_purchasereturnpayments` FOREIGN KEY (`return_id`) REFERENCES `purchasereturn` (`return_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `fk_tax_id` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`);

--
-- Constraints for table `salesitems`
--
ALTER TABLE `salesitems`
  ADD CONSTRAINT `fk_item_id_salesitem` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `fk_sales_id_salesitem` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`sales_id`);

--
-- Constraints for table `salespayments`
--
ALTER TABLE `salespayments`
  ADD CONSTRAINT `fk_salesid_salespayment` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`sales_id`);

--
-- Constraints for table `salesreturn`
--
ALTER TABLE `salesreturn`
  ADD CONSTRAINT `fk_customer_id_salesreturn` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `fk_salesid_salesreturn` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`sales_id`),
  ADD CONSTRAINT `fk_tax_id_salesreturn` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`);

--
-- Constraints for table `salesreturnitems`
--
ALTER TABLE `salesreturnitems`
  ADD CONSTRAINT `fk_item_id_salesreturnitems` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `fk_return_id_salesreturnitems` FOREIGN KEY (`return_id`) REFERENCES `salesreturn` (`return_id`),
  ADD CONSTRAINT `fk_sales_id_salesreturnitems` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`sales_id`);

--
-- Constraints for table `salesreturnpayments`
--
ALTER TABLE `salesreturnpayments`
  ADD CONSTRAINT `fk_return_id_salesreturnpayments` FOREIGN KEY (`return_id`) REFERENCES `salesreturn` (`return_id`),
  ADD CONSTRAINT `fk_sales_id_salesreturnpayments` FOREIGN KEY (`sales_id`) REFERENCES `salesreturn` (`sales_id`);

--
-- Constraints for table `stockentry`
--
ALTER TABLE `stockentry`
  ADD CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role_id_users` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
