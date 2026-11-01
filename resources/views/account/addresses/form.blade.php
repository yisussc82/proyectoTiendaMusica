<div class="space-y-4">
    <x-form.input name="address_line_1" :label="__('Address')" :value="$address->address_line_1" required />
    <x-form.input name="address_line_2" :label="__('Address (line 2)')" :value="$address->address_line_2" />
    <x-form.input name="city" :label="__('City')" :value="$address->city" required />
    <x-form.select name="province" :label="__('Province')" :options="$provinces" :selected="$address->province" :placeholder="__('Choose a province')" required />
    <x-form.input name="postal_code" :label="__('Postal code')" :value="$address->postal_code" maxlength="5" required />
    <x-form.checkbox name="is_default" :label="__('Use as default address')" :checked="$address->is_default" />
</div>
