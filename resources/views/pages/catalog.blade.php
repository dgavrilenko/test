@extends('layouts.app')
@section('title', 'Каталог')

<main id = "app" role="main" class="container" style="padding-top: 160px;">
    <div class="row">
        <div class="col-md-12">
            <div class="product-content">
                <h1> Каталог </h1>
                <catalog-component />
            </div><!-- /.blog-post -->
        </div><!-- /.blog-main -->
    </div><!-- /.row -->
    <notifications group="app" animation-type="velocity" />
</main><!-- /.container -->
