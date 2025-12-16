<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 取得全站合約變數的「搜尋字串 => 取代值」對照表
 *
 * 同時支援：
 *  - 'company_name' => 'XXX有限公司'
 *  - 'company_name' => [ 'key' => 'company_name', 'value' => 'XXX有限公司' ]
 */
function woc_get_global_var_pairs() {

    $vars = get_option( 'woc_contract_global_vars', [] );

    if ( ! is_array( $vars ) ) {
        return [];
    }

    $pairs = [];

    foreach ( $vars as $idx => $row ) {

        if ( is_array( $row ) ) {
            // 舊版結構：陣列裡有 key / value
            $key   = isset( $row['key'] )   ? $row['key']   : $idx;
            $value = isset( $row['value'] ) ? $row['value'] : '';
        } else {
            // 目前你的結構：key => '純字串'
            $key   = $idx;
            $value = $row;
        }

        $key   = sanitize_key( trim( (string) $key ) );
        $value = (string) $value;

        if ( $key === '' || $value === '' ) {
            continue;
        }

        // 組成 {company_name}
        $pairs[ '{' . $key . '}' ] = $value;
    }

    return $pairs;
}

/**
 * 將內容中的 {var_key} 用全站變數值取代
 */
function woc_replace_contract_vars( $content ) {

    if ( ! is_string( $content ) || $content === '' ) {
        return $content;
    }

    $pairs = woc_get_global_var_pairs();

    if ( empty( $pairs ) ) {
        return $content;
    }

    return strtr( $content, $pairs );
}
