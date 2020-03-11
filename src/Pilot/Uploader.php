<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;

class Uploader
{

    public static function singleImage($name, $value = null, $id = null, $params = [])
    {
        if (is_null($id)) {
            $id = $name;
        }

        ?>

        <div class="uploader-new uploader-combo">
            <a href="" class="uploader-empty btn-upload" data-params="
            <?php echo http_build_query($params); ?>" data-target="<?php echo $id; ?>">
                <i class="fa fa-picture-o fa-2x"></i>
                <p>Select Image</p>
            </a>

            <div class="uploader-combo-preview"></div>

            <button class="btn btn-default uploader-combo-delete"><i class="fa fa-trash fa-2x"></i></button>

            <div class="uploader-combo-input">
                <?php echo \Form::hidden($name, null, ['id' => $id]); ?>
            </div>
        </div>
        <?php
    }

    public static function button($name, $label, $value = null, $id = null, $params = [])
    {
        if (is_null($id)) {
            $id = $name;
        }

        ?>
        <div class="uploader-combo form-group row">

            <div class="col-lg-4">

                <div class="uploader-combo-input">

                    <?php if (! empty($label)) {
                        echo \Form::label($id, $label);
                    } ?>

                    <?php echo \Form::hidden($name, null, array('class' => 'form-control', 'id' => $id)); ?>

                    <div><button class="btn btn-success btn-upload btn-block" type="button" data-params="
                    <?php echo http_build_query($params); ?>" data-target="<?php echo $id; ?>">
                    <i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;Upload</button></div>

                </div>

                <div class="uploader-combo-preview" style="margin-top: 10px;"></div>

                <a href="#" class="uploader-combo-delete"><i class="fa fa-close"></i> Remove Image</a>

            </div>

        </div>
        <?php
    }

    public static function input($name, $label, $value = null, $id = null, $params = [])
    {
        if (is_null($id)) {
            $id = $name;
        }

        $id = Str::slug($id);

        ?>
        <div class="uploader-combo form-group">

            <div class="uploader-combo-input">

                <?php if (! empty($label)) {
                    echo \Form::label($id, $label);
                } ?>

                <div class="input-group">

                    <?php echo \Form::text($name, $value, array('class' => 'form-control', 'id' => $id)); ?>

                    <span class="input-group-btn">
                        <button class="btn btn-success btn-upload" type="button" data-params="
                        <?php echo http_build_query($params); ?>" data-target="<?php echo $id; ?>">
                        <i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;Upload</button>
                    </span>

                </div>

            </div>

            <div class="uploader-combo-preview" style="margin-top: 10px;"></div>

        </div>
        <?php
    }

    public static function helper()
    {
        ?>
        <!-- Upload Form -->
        <div class="uploader" style="visibility: hidden;">
            <?php echo \Form::open(array(
                'route' => 'assets.upload',
                'method' => 'post', 'files' => true,
                'id' => 'upload-form')); ?>

                <input type="hidden" name="params" id="file-upload-params">
                <?php echo \Form::file('file', array('id' => 'file-upload-input')); ?>

            <?php echo \Form::close(); ?>
        </div>
        <?php
    }
}
