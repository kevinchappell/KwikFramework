<?php

  Class KwikInputs{

    public function positions() {
      $positions = array(
        '0 0' => 'Top Left',
        '0 50%' => 'Top Center',
        '0 100%' => 'Top Right',
        '50% 0' => 'Middle Left',
        '50% 50%' => 'Middle Center',
        '50% 100%' => 'Middle Right',
        '100% 0' => 'Bottom Left',
        '100% 50%' => 'Bottom Center',
        '100% 100%' => 'Bottom Right',
      );
      return $positions;
    }

    public function repeat() {
      $R = 'Repeat';
      $r = strtolower($R);
      $repeat = array(
        'no-'.$r => 'No '.$R,
        $r => $R,
        $r.'-x' => $R.'-X',
        $r.'-y' => $R.'-Y',
      );
      return $repeat;
    }

    public function target() {
      $target = array(
        '_blank' => 'New Window/Tab',
        '_self' => 'Same Window'
      );
      return $target;
    }

    public function bgSize() {
      $bgSize = array(
        'auto' => 'Default',
        '100% 100%' => 'Stretch',
        'cover' => 'Cover',
      );
      return $bgSize;
    }

    public function bgAttachment() {
      $bgAttachment = array(
        'scroll' => 'Scroll',
        'fixed' => 'Fixed',
      );
      return $bgAttachment;
    }

    public function fontWeights() {
      $fontWeights = array(
        'normal' => 'Normal',
        'bold' => 'Bold',
        'bolder' => 'Bolder',
        'lighter' => 'Lighter',
      );
      return $fontWeights;
    }


    /**
     * Generate markup for input field
     * @param  [Object] $attrs Object with properties for field attributes
     * @return [String]        markup for desired input field
     */
    public function input($attrs) {
      $output = '';
      if($attrs['label'] && !is_null($attrs['label'])) {
        $output = $this->markup('label', $attrs['label'], array( 'for' => $attrs['id']));
        unset($attrs['label']);
      }
      $output .= '<input ' . $this->attrs($attrs) . ' />';

      if($attrs['type'] !== 'hidden' && !is_null($attrs['type'])){
        $output = $this->markup('div', $output, array('class'=>KF_PREFIX.'field kf_'.$attrs['type'].'_wrap'));
      }
      return $output;
    }


    public function img($name, $val, $label, $attrs) { // TODO extend $attrs

      wp_enqueue_media();
      $output = '';
      if($val){
        $thumb = wp_get_attachment_image_src($val, 'thumbnail');
        $thumb = $thumb['0'];
      }
      $defaultAttrs = array(
        'type' => 'hidden',
        'name' => $name,
        'class' => 'img_id',
        'value' => $val,
        'id' => $this->makeID($name)
      );
      $attrs = array_merge($defaultAttrs, $attrs);

      if($label) {
        $attrs->label = esc_attr($label);
      }

      $output .= $this->input($attrs);
      $img_attrs = array("class"=>"img_prev", "width"=>"23", "height"=>"23", "title"=>get_the_title($val));
      $output .= $this->markup('img', NULL, $img_attrs);
      $output .= '<span class="img_title">' . get_the_title($val) . (!empty($val) ? '<span title="' . __('Remove Image', 'kwik') . '" class="clear_img tooltip"></span>' : '') . '</span><input type="button" class="upload_img" id="upload_img" value="+ ' . __('IMG', 'kwik') . '" />';
      $output = $this->markup('div', $output, array('class'=>KF_PREFIX.'field kf_img_wrap'));
      return $output;
    }

    public function text($name, $val, $label = NULL, $attrs = NULL) {
      $output = '';
      $defaultAttrs =   array(
        'type' => 'text',
        'name' => $name,
        'class' => KF_PREFIX.'text',
        'value' => $val,
        'id' => $this->makeID($name),
        'label' => esc_attr($label)
      );
      if(!is_null($attrs)){
        $attrs = array_merge($defaultAttrs, $attrs);
      }

      $output .= $this->input($attrs);

      return $output;
    }

    public function link($name, $val, $label = NULL, $attrs = NULL) {
      $output = '';

      $defaultAttrs =   array(
        'type' => 'text',
        'name' => $name.'[url]',
        'class' => KF_PREFIX.'link',
        'value' => $val['url'],
        'id' => $this->makeID($name)
      );
      if(!is_null($attrs)){
        $attrs = array_merge($defaultAttrs, $attrs);
      }

      if($label) {
        $attrs['label'] = esc_attr($label);
      }

      $output .= $this->input($attrs);
      $output .= $this->select($name.'[target]', $val['target'], $this->target());
      $output = $this->markup('div', $output, array('class'=>KF_PREFIX.'link_wrap'));

      return $output;
    }

    public function nonce($name, $val) {
      $attrs = array(
        'type' => 'hidden',
        'name' => $name,
        'value' => $val,
      );
      return $this->input($attrs);
    }

    public function spinner($name, $val, $label = NULL) {
      $output = '';
      $attrs = array(
        'type' => 'number',
        'name' => $name,
        'class' => KF_PREFIX.'spinner',
        'max' => '50',
        'min' => '1',
        'value' => $val,
        'label'=> $label
      );

      if($label) {
        $attrs->label = esc_attr($label);
      }
      $output .= $this->input($attrs);

      return $output;
    }

    public function color($name, $val, $label = NULL) {
      $output = '';
      wp_enqueue_script('cpicker', KF_URL . '/js/cpicker.js');

      $attrs = array(
        'type' => 'text',
        'name' => $name,
        'class' => 'cpicker',
        'value' => $val,
        'id' => $this->makeID($name)
      );
      if($label) {
        $attrs->label = esc_attr($label);
      }
      $output .= $this->input($attrs);
      if (!empty($val)) {$output .= '<span class="clear_color tooltip" title="' . __('Remove Color', 'kwik') . '"></span>';
      }

      return $output;
    }

    public function select($name, $val, $optionsArray, $label = NULL) {

      $attrs = array(
        'name' => $name,
        'class' => KF_PREFIX.'select',
        'id' => $this->makeID($name)
      );

      $output = '';

      if($label) {
        $output .= $this->markup('label', $label, array( 'for' => $attrs->id));
      }
        $options = '';

        foreach ($optionsArray as $k => $v) {
        $oAttrs = array(
          'value' => $k
          );
        if ($val === $k) {
          $oAttr['selected'] = 'selected';
        }
        $options .= $this->markup('option', $v, $oAttrs);
        // $options[$k] = '<option ' . selected($k, $val, false) . ' value="' . $k . '">' . $v . '</option>';
      }

      $output .= $this->markup('select', $options, $attrs); // TODO finish refactor


      // $output .= '</select>';
      $output = $this->markup('div', $output, array('class'=>KF_PREFIX.'field '.KF_PREFIX.'select_wrap'));

      return $output;
    }

    public function fontFamily($name, $val) {
      $utils = new KwikUtils();
      $fonts = $utils->get_google_fonts($api_key);  // TODO: Api key from settings
      $options = array();
      foreach ($fonts as $font) {
        $options[str_replace(' ', '+', $font->family)] = $font->family;
      }
      return $this->select($name, $val, $options);
    }



    private function attrs($attrs) {
      $output = '';
      if (is_array($attrs)) {
        if($attrs['label']) {
          unset($attrs['label']);
        }
        foreach ($attrs as $key => $val) {
          if (is_array($val)) {
            $val = implode(" ", $val);
          }
          $output .= $key . '="' . esc_attr($val) . '" ';
        }
      }
      return $output;
    }

    private function makeID($string){
      $string = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
      return trim(preg_replace('/-+/', '-', $string), '-');
    }

    public function markup($tag, $content = NULL, $attrs = NULL){

      $markup = '<'.$tag.' '.$this->attrs($attrs).' '.($tag === 'img' ? '/' : '').'>';
      if($content) $markup .= $content . ($tag === 'label' ? ':' : '');
      if($tag !== 'img') $markup .= '</'.$tag.'>';


      return $markup;
    }

  }//---------/ Class KwikInputs
