package org.gipilab.observatoiredestaux;

/*
 * Observatoire des taux
 *
 * Copyright Thibault et Gilbert Mondary, Laboratoire de Recherche pour le Développement Local (2006--)
 *
 * labo@gipilab.org
 *
 * Ce logiciel est un programme informatique servant à visualiser différents indicateurs sur les taux
 * (historique, courbes des taux, pression conjoncturelle...)
 *
 *
 * Ce logiciel est régi par la licence CeCILL soumise au droit français et
 * respectant les principes de diffusion des logiciels libres. Vous pouvez
 * utiliser, modifier et/ou redistribuer ce programme sous les conditions
 * de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
 * sur le site "http://www.cecill.info".
 *
 * En contrepartie de l'accessibilité au code source et des droits de copie,
 * de modification et de redistribution accordés par cette licence, il n'est
 * offert aux utilisateurs qu'une garantie limitée. Pour les mêmes raisons,
 * seule une responsabilité restreinte pèse sur l'auteur du programme, le
 * titulaire des droits patrimoniaux et les concédants successifs.
 *
 * A cet égard l'attention de l'utilisateur est attirée sur les risques
 * associés au chargement, à l'utilisation, à la modification et/ou au
 * développement et à la reproduction du logiciel par l'utilisateur étant
 * donné sa spécificité de logiciel libre, qui peut le rendre complexe à
 * manipuler et qui le réserve donc à des développeurs et des professionnels
 * avertis possédant des connaissances informatiques approfondies. Les
 * utilisateurs sont donc invités à charger et tester l'adéquation du
 * logiciel à leurs besoins dans des conditions permettant d'assurer la
 * sécurité de leurs systèmes et ou de leurs données et, plus généralement,
 * à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
 *
 * Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
 * pris connaissance de la licence CeCILL, et que vous en avez accepté les
 * termes.
 *
 */


import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.MenuItem;
import android.view.View;
import android.webkit.URLUtil;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.google.android.material.navigation.NavigationView;

import java.util.Objects;


public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener {

    private String currentUrl = UrlTable.URLPRESENTATION;
    private long back_pressed = 0L;
    private myWebViewClient myBrowser = null;
    private ProgressBar progress = null;
    private SharedPreferences mSettings = null;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        progress = findViewById(R.id.progressBar);

        mSettings = PreferenceManager.getDefaultSharedPreferences(this);


        NavigationView navigationView = findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        myBrowser = new myWebViewClient();

        final SwipeRefreshLayout swipeContainer = findViewById(R.id.swipeLayout);
        swipeContainer.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                WebView webView = findViewById(R.id.webView);
                webView.clearCache(true);
                myBrowser.myLoadUrl(webView, currentUrl);
                swipeContainer.setRefreshing(false);
            }
        });


        WebView webView = findViewById(R.id.webView);

        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);

        settings.setSupportZoom(false);
        settings.setBuiltInZoomControls(false);
        settings.setDisplayZoomControls(false);

        webView.setLayerType(View.LAYER_TYPE_HARDWARE, null);

        webView.setWebViewClient(myBrowser);

        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {

                progress.setProgress(newProgress);
                if (newProgress == 100) {
                    progress.setVisibility(View.INVISIBLE);

                } else {
                    progress.setVisibility(View.VISIBLE);

                }

            }
        });

        currentUrl = mSettings.getString(getString(R.string.editorPrefLastUrlKey), UrlTable.URLPRESENTATION);

        myBrowser.myLoadUrl(webView, currentUrl);

        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        drawer.openDrawer(GravityCompat.START);

    }

    @Override
    public void onBackPressed() {

        if (back_pressed + R.integer.toastExitDuration > System.currentTimeMillis()) {
            super.onBackPressed();
        } else {
            Toast.makeText(getBaseContext(),
                    "Appuyez à nouveau pour quitter", Toast.LENGTH_SHORT)
                    .show();
        }
        back_pressed = System.currentTimeMillis();
    }

    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        WebView webView = findViewById(R.id.webView);

        if (id == R.id.buttonCourbesDesTauxVariables) {
            myBrowser.myLoadUrl(webView, UrlTable.URLCOURBESTAUXVARIABLES);
        } else if (id == R.id.buttonCourbesDesTauxFixes) {
            myBrowser.myLoadUrl(webView, UrlTable.URLCOURBESTAUXFIXES);
        } else if (id == R.id.buttonHistoriqueTauxVariables) {
            myBrowser.myLoadUrl(webView, UrlTable.URLHISTORIQUETAUXVARIABLES);
        } else if (id == R.id.buttonHistoriqueTauxFixes) {
            myBrowser.myLoadUrl(webView, UrlTable.URLHISTORIQUETAUXFIXES);
        } else if (id == R.id.buttonPressionConjoncturelleTauxVariables) {
            myBrowser.myLoadUrl(webView, UrlTable.URLPRESSIONCONJONCTURELLETAUXVARIABLES);
        } else if (id == R.id.buttonPressionConjoncturelleTauxFixes) {
            myBrowser.myLoadUrl(webView, UrlTable.URLPRESSIONCONJONCTURELLETAUXFIXES);
        } else if (id == R.id.buttonJauges) {
            myBrowser.myLoadUrl(webView, UrlTable.URLJAUGES);
        } else if (id == R.id.buttonVolatiliteTauxVariables) {
            myBrowser.myLoadUrl(webView, UrlTable.URLVOLATILITETAUXVARIABLES);
        } else if (id == R.id.buttonVolatiliteTauxFixes) {
            myBrowser.myLoadUrl(webView, UrlTable.URLVOLATILITETAUXFIXES);
        } else if (id == R.id.buttonGFM) {
            myBrowser.myLoadUrl(webView, UrlTable.URLTHEORIEGFM);
        } else if (id == R.id.buttonPresentation) {
            myBrowser.myLoadUrl(webView, UrlTable.URLPRESENTATION);
        } else if (id == R.id.buttonLicence) {
            webView.loadUrl("file:///android_asset/cecill_fr.html");
        } else if (id == R.id.buttonSite) {
            String url = "https://gipilab.org";
            Uri uri = Uri.parse(url);
            Intent intent = new Intent(Intent.ACTION_VIEW, uri);
            if (intent.resolveActivity(getPackageManager()) != null) {
                startActivity(intent);
            }
        }


        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);

        return true;
    }

    private class myWebViewClient extends WebViewClient {


        private boolean isConnected() {
            ConnectivityManager cm =
                    (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);

            NetworkInfo activeNetwork = Objects.requireNonNull(cm).getActiveNetworkInfo();
            return (activeNetwork != null && activeNetwork.isConnectedOrConnecting());
        }


        @Override
        public boolean shouldOverrideUrlLoading(@NonNull WebView view, String url) {
            myLoadUrl(view, url);
            return true;
        }

        @Override
        public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
            super.onReceivedError(view, errorCode, description, failingUrl);
            view.loadUrl("file:///android_asset/neterror.html");

        }

        @Override
        public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
            super.onReceivedError(view, request, error);
            view.loadUrl("file:///android_asset/neterror.html");

        }

        @TargetApi(Build.VERSION_CODES.N)
        @Override
        public boolean shouldOverrideUrlLoading(@NonNull WebView view, @NonNull WebResourceRequest request) {
            myLoadUrl(view, request.getUrl().toString());
            return true;
        }

        private void myLoadUrl(@NonNull WebView webView, String url) {
            if (!URLUtil.isValidUrl(url)) {
                return;
            }

            currentUrl = url;

            if (!isConnected()) {
                webView.loadUrl("file:///android_asset/nonet.html");
            } else {
                SharedPreferences.Editor editor = mSettings.edit();
                long timestamp = System.currentTimeMillis() / 1000;
                if (timestamp > mSettings.getLong("lastRefresh", 0) + R.integer.maxCacheSeconds) {
                    webView.clearCache(true);
                    editor.putLong("lastRefresh", timestamp);
                }
                editor.putString(getString(R.string.editorPrefLastUrlKey), url);
                editor.apply();
                webView.loadUrl(url);
            }
        }
    }
}
