{{ extends "layout/default" }}

{{ block "title"}}
    view title
{{ /block }}

{{ block "main" }}
    {{ include "demo/sidebar" }}

	name: <?= $this->name ?>
	<br />
	address: <?= $this->address ?>
{{ /block }}