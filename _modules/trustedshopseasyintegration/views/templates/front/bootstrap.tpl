
{*
  {if !empty($widgetScriptModels)}
      {foreach $widgetScriptModels as $widgetScriptModel}
          {if !empty($widgetScriptModel->getAttributes())}
            <{$widgetScriptModel->getTag()|escape:'htmlall':'UTF-8'}
              {foreach $widgetScriptModel->getAttributes() as $attributeName => $value}
                  {$attributeName|escape:'htmlall':'UTF-8'}="{$value|escape:'htmlall':'UTF-8'}"
              {/foreach}
            ></{$widgetScriptModel->getTag()|escape:'htmlall':'UTF-8'}>
          {/if}
      {/foreach}
  {/if}
*}